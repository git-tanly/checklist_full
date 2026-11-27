<?php

namespace Tests\Feature;

use App\Models\ChecklistAnswer;
use App\Models\ChecklistQuestion;
use App\Models\ChecklistSection;
use App\Models\ChecklistSubmission;
use App\Models\Employee;
use App\Models\Restaurant;
use App\Models\User;

class OperationalChecklistReviewTest extends OperationalChecklistTestCase
{
    private Restaurant $outletA;

    private Restaurant $outletB;

    private User $managerA;

    private User $managerB;

    private User $superAdmin;

    private ChecklistSubmission $submissionA;

    private ChecklistSubmission $submissionB;

    protected function setUp(): void
    {
        parent::setUp();

        // Role dibutuhkan oleh Spatie; buat bila belum ada.
        foreach (['Super Admin', 'Restaurant Manager'] as $roleName) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
        }

        $this->outletA = $this->makeRestaurant('209', '209 Dining');
        $this->outletB = $this->makeRestaurant('XFH', 'Xiang Fu Hai');

        $this->makeAllDayWindow('lunch');

        $this->managerA = $this->makeManager('manager.a@test.local', 'Manager A', $this->outletA);
        $this->managerB = $this->makeManager('manager.b@test.local', 'Manager B', $this->outletB);

        $this->superAdmin = User::create([
            'name' => 'Super Test',
            'email' => 'super@test.local',
            'password' => 'password',
        ]);
        $this->superAdmin->assignRole('Super Admin');

        $sectionA = $this->makeSection($this->outletA, 'VERIFY A STATION');
        $questionA = $this->makeQuestion($sectionA, 'lunch', ['check_point' => 'Fridge temp', 'answer_type' => 'number', 'temp_max' => 5, 'unit' => 'C']);
        $employeeA = $this->makeEmployee($this->outletA, 'Staff A');

        $this->submissionA = $this->makeSubmission($this->outletA, $sectionA, $employeeA, [
            $questionA->id => [
                'result' => 'not_ok', 'answer_number' => 8.5, 'check_point' => 'Fridge temp',
                'answer_type' => 'number', 'unit' => 'C', 'temp_max' => 5,
            ],
        ]);

        $sectionB = $this->makeSection($this->outletB, 'VERIFY B STATION');
        $questionB = $this->makeQuestion($sectionB, 'lunch', ['check_point' => 'Clean station']);
        $employeeB = $this->makeEmployee($this->outletB, 'Staff B');

        $this->submissionB = $this->makeSubmission($this->outletB, $sectionB, $employeeB, [
            $questionB->id => ['result' => 'ok', 'answer_boolean' => true, 'check_point' => 'Clean station', 'answer_type' => 'yes_no'],
        ]);
    }

    public function test_manager_only_sees_own_outlet_submissions(): void
    {
        $this->actingAs($this->managerA);

        $response = $this->get(route('operational-checklists.index'));

        $response->assertOk();
        $response->assertSee('209 Dining');
        $response->assertDontSee('Xiang Fu Hai');
    }

    public function test_manager_can_view_submission_detail(): void
    {
        $this->actingAs($this->managerA);

        $response = $this->get(route('operational-checklists.show', $this->submissionA->id));

        $response->assertOk();
        $response->assertSee('Fridge temp');
        $response->assertSee('NOT OK');
        $response->assertSee('Aksi Verifikasi');
    }

    public function test_manager_from_other_outlet_cannot_verify(): void
    {
        $this->actingAs($this->managerB);

        $response = $this->patch(route('operational-checklists.verify', $this->submissionA->id));

        $response->assertRedirect(route('operational-checklists.index'));
        $response->assertSessionHas('error');
        $this->assertSame('pending', $this->submissionA->fresh()->status);
    }

    public function test_super_admin_can_verify_any_submission(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->patch(route('operational-checklists.verify', $this->submissionA->id));

        $response->assertRedirect();
        $this->assertSame('verified', $this->submissionA->fresh()->status);
        $this->assertSame($this->superAdmin->id, $this->submissionA->fresh()->verified_by);
    }

    public function test_reject_requires_note(): void
    {
        $this->actingAs($this->managerB);

        $response = $this->patch(route('operational-checklists.reject', $this->submissionB->id), []);

        $response->assertSessionHasErrors('verification_note');
        $this->assertSame('pending', $this->submissionB->fresh()->status);
    }

    public function test_reject_then_resubmit_resets_submission_to_pending(): void
    {
        $this->actingAs($this->managerB);

        $this->patch(route('operational-checklists.reject', $this->submissionB->id), [
            'verification_note' => 'Mohon dicek ulang suhu kulkas.',
        ]);

        $this->assertSame('rejected', $this->submissionB->fresh()->status);

        // Staff memperbaiki submission yang sama (alur publik).
        $this->travelTo(now()->setTime(13, 0));
        $questionB = ChecklistQuestion::where('section_id', $this->submissionB->section_id)->first();
        $employeeB = Employee::find($this->submissionB->employee_id);

        $response = $this->post(route('operational.submit', [$this->outletB->checklist_token, $this->submissionB->section_id]), [
            'employee_id' => $employeeB->id,
            'answers' => [
                ['question_id' => $questionB->id, 'value' => 'yes'],
            ],
        ]);

        $response->assertRedirect(route('operational.submitted', $this->outletB->checklist_token));

        $fresh = $this->submissionB->fresh();
        $this->assertSame('pending', $fresh->status);
        $this->assertNull($fresh->verified_by);
        $this->assertNull($fresh->verified_at);
        $this->assertNull($fresh->verification_note);
        $this->assertSame(1, ChecklistSubmission::where('section_id', $this->submissionB->section_id)->count());
    }

    private function makeManager(string $email, string $name, Restaurant $outlet): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
        ]);

        $user->assignRole('Restaurant Manager');
        $user->restaurants()->sync([$outlet->id]);

        return $user;
    }

    private function makeSubmission(Restaurant $outlet, ChecklistSection $section, Employee $employee, array $answers): ChecklistSubmission
    {
        $submission = ChecklistSubmission::create([
            'restaurant_id' => $outlet->id,
            'section_id' => $section->id,
            'employee_id' => $employee->id,
            'session_type' => 'lunch',
            'date' => now()->toDateString(),
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        foreach ($answers as $questionId => $answer) {
            ChecklistAnswer::create(array_merge([
                'submission_id' => $submission->id,
                'question_id' => $questionId,
            ], $answer));
        }

        return $submission;
    }
}
