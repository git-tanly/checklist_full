<?php

namespace Tests\Feature;

use App\Models\ChecklistQuestion;
use App\Models\ChecklistSubmission;
use App\Models\Employee;
use App\Models\Restaurant;

class OperationalChecklistFlowTest extends OperationalChecklistTestCase
{
    private Restaurant $restaurant;

    private $section;

    private Employee $employee;

    private ChecklistQuestion $yesQuestion;

    private ChecklistQuestion $tempQuestion;

    private ChecklistQuestion $rangeQuestion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = $this->makeRestaurant('209', '209 Dining');
        $this->makeAllDayWindow('lunch');
        $this->section = $this->makeSection($this->restaurant, 'FLOW STATION');
        $this->employee = $this->makeEmployee($this->restaurant, 'Staff Flow');

        $this->yesQuestion = $this->makeQuestion($this->section, 'lunch', ['check_point' => 'Station clean']);
        $this->tempQuestion = $this->makeQuestion($this->section, 'lunch', [
            'check_point' => 'Fridge temp',
            'answer_type' => 'number',
            'temp_max' => 5,
            'unit' => 'C',
            'sort_order' => 2,
        ]);
        $this->rangeQuestion = $this->makeQuestion($this->section, 'lunch', [
            'check_point' => 'Item count',
            'answer_type' => 'number',
            'std_min' => 4,
            'std_max' => 10,
            'sort_order' => 3,
        ]);
    }

    public function test_entry_page_can_be_opened_without_login(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $response = $this->get(route('operational.entry', $this->restaurant->checklist_token));

        $response->assertOk();
        $response->assertSee($this->restaurant->name);
        $response->assertSee('FLOW STATION');
    }

    public function test_section_page_shows_active_session_questions(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $response = $this->get(route('operational.section', [$this->restaurant->checklist_token, $this->section->id]));

        $response->assertOk();
        $response->assertSee('Station clean');
        $response->assertSee('Fridge temp');
        $response->assertSee('Staff Flow');
    }

    public function test_successful_submit_creates_pending_submission_with_correct_results(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $response = $this->post(route('operational.submit', [$this->restaurant->checklist_token, $this->section->id]), [
            'employee_id' => $this->employee->id,
            'answers' => [
                ['question_id' => $this->yesQuestion->id, 'value' => 'yes'],
                ['question_id' => $this->tempQuestion->id, 'value' => '8.5', 'remarks' => 'door left open'],
                ['question_id' => $this->rangeQuestion->id, 'value' => '7'],
            ],
        ]);

        $response->assertRedirect(route('operational.submitted', $this->restaurant->checklist_token));

        $submission = ChecklistSubmission::query()->firstOrFail();
        $this->assertSame('pending', $submission->status);
        $this->assertSame($this->employee->id, $submission->employee_id);
        $this->assertSame('lunch', $submission->session_type);
        $this->assertSame(now()->toDateString(), $submission->date->toDateString());
        $this->assertCount(3, $submission->answers);

        $results = $submission->answers->pluck('result', 'check_point');
        $this->assertSame('ok', $results['Station clean']);
        $this->assertSame('not_ok', $results['Fridge temp']);
        $this->assertSame('ok', $results['Item count']);

        $tempAnswer = $submission->answers->firstWhere('check_point', 'Fridge temp');
        $this->assertSame('8.50', (string) $tempAnswer->answer_number);
        $this->assertSame('door left open', $tempAnswer->remarks);
        $this->assertSame('5.00', (string) $tempAnswer->temp_max);
    }

    public function test_second_submit_for_same_section_and_session_is_rejected(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $payload = [
            'employee_id' => $this->employee->id,
            'answers' => [
                ['question_id' => $this->yesQuestion->id, 'value' => 'yes'],
                ['question_id' => $this->tempQuestion->id, 'value' => '4'],
                ['question_id' => $this->rangeQuestion->id, 'value' => '5'],
            ],
        ];

        $this->post(route('operational.submit', [$this->restaurant->checklist_token, $this->section->id]), $payload);
        $this->assertSame(1, ChecklistSubmission::count());

        $response = $this->from('/')->post(route('operational.submit', [$this->restaurant->checklist_token, $this->section->id]), $payload);

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
        $this->assertSame(1, ChecklistSubmission::count());
    }

    public function test_employee_from_another_outlet_is_rejected(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $otherRestaurant = $this->makeRestaurant('XFH', 'Xiang Fu Hai');
        $this->makeEmployee($otherRestaurant, 'Staff Other', 'Cashier');

        $response = $this->from('/')->post(route('operational.submit', [$this->restaurant->checklist_token, $this->section->id]), [
            'employee_id' => Employee::where('restaurant_id', $otherRestaurant->id)->value('id'),
            'answers' => [
                ['question_id' => $this->yesQuestion->id, 'value' => 'yes'],
                ['question_id' => $this->tempQuestion->id, 'value' => '4'],
                ['question_id' => $this->rangeQuestion->id, 'value' => '5'],
            ],
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('employee_id');
        $this->assertSame(0, ChecklistSubmission::count());
    }

    public function test_questions_from_other_session_cannot_be_smuggled(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $dinnerQuestion = $this->makeQuestion($this->section, 'dinner', ['check_point' => 'Dinner only']);

        $response = $this->from('/')->post(route('operational.submit', [$this->restaurant->checklist_token, $this->section->id]), [
            'employee_id' => $this->employee->id,
            'answers' => [
                ['question_id' => $this->yesQuestion->id, 'value' => 'yes'],
                ['question_id' => $this->tempQuestion->id, 'value' => '4'],
                ['question_id' => $this->rangeQuestion->id, 'value' => '5'],
                ['question_id' => $dinnerQuestion->id, 'value' => 'yes'],
            ],
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
        $this->assertSame(0, ChecklistSubmission::count());
    }

    public function test_thresholds_are_not_exposed_on_public_pages(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $sectionHtml = $this->get(route('operational.section', [$this->restaurant->checklist_token, $this->section->id]))->getContent();
        $entryHtml = $this->get(route('operational.entry', $this->restaurant->checklist_token))->getContent();

        foreach ([$sectionHtml, $entryHtml] as $html) {
            foreach (['std_min', 'std_max', 'temp_max', (string) $this->tempQuestion->temp_max] as $needle) {
                $this->assertStringNotContainsString($needle, $html);
            }
        }

        $this->assertStringContainsString('Fridge temp', $sectionHtml);
    }
}
