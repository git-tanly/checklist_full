<?php

namespace Tests\Unit;

use App\Models\ChecklistQuestion;
use App\Services\Operational\ChecklistAnswerEvaluator;
use PHPUnit\Framework\TestCase;

class ChecklistAnswerEvaluatorTest extends TestCase
{
    private ChecklistAnswerEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->evaluator = new ChecklistAnswerEvaluator;
    }

    public function test_temperature_within_limit_is_ok(): void
    {
        $question = $this->numberQuestion(['temp_max' => 5]);

        $this->assertSame('ok', $this->evaluator->evaluate($question, 4.5));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 5));
    }

    public function test_temperature_above_limit_is_not_ok(): void
    {
        $question = $this->numberQuestion(['temp_max' => 5]);

        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 5.01));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 12));
    }

    public function test_only_std_min_uses_lower_bound(): void
    {
        $question = $this->numberQuestion(['std_min' => 2]);

        $this->assertSame('ok', $this->evaluator->evaluate($question, 2));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 99));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 1.99));
    }

    public function test_only_std_max_uses_upper_bound(): void
    {
        $question = $this->numberQuestion(['std_max' => 10]);

        $this->assertSame('ok', $this->evaluator->evaluate($question, 0));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 10));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 10.01));
    }

    public function test_range_is_inclusive_on_both_ends(): void
    {
        $question = $this->numberQuestion(['std_min' => 2, 'std_max' => 10]);

        $this->assertSame('ok', $this->evaluator->evaluate($question, 2));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 6));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 10));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 1.99));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 10.01));
    }

    public function test_temp_max_wins_over_std_range(): void
    {
        $question = $this->numberQuestion([
            'temp_max' => 5,
            'std_min' => 0,
            'std_max' => 10,
        ]);

        $this->assertSame('ok', $this->evaluator->evaluate($question, 5));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 7));
    }

    public function test_question_without_threshold_is_always_ok(): void
    {
        $question = $this->numberQuestion();

        $this->assertSame('ok', $this->evaluator->evaluate($question, 0));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 1500.75));
    }

    public function test_decimal_values_are_compared_numerically(): void
    {
        $question = $this->numberQuestion(['std_min' => 1.5, 'std_max' => 2.5]);

        $this->assertSame('ok', $this->evaluator->evaluate($question, '1.50'));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 2.49));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, '1.49'));
    }

    public function test_yes_no_answers(): void
    {
        $question = $this->yesNoQuestion();

        $this->assertSame('ok', $this->evaluator->evaluate($question, 'yes'));
        $this->assertSame('ok', $this->evaluator->evaluate($question, true));
        $this->assertSame('ok', $this->evaluator->evaluate($question, 1));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 'no'));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, false));
        $this->assertSame('not_ok', $this->evaluator->evaluate($question, 0));
    }

    public function test_empty_answer_is_not_ok(): void
    {
        $this->assertSame('not_ok', $this->evaluator->evaluate($this->numberQuestion(['temp_max' => 5]), null));
        $this->assertSame('not_ok', $this->evaluator->evaluate($this->yesNoQuestion(), ''));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function numberQuestion(array $attributes = []): ChecklistQuestion
    {
        return new ChecklistQuestion(array_merge([
            'answer_type' => 'number',
            'check_point' => 'Sample checkpoint',
        ], $attributes));
    }

    private function yesNoQuestion(): ChecklistQuestion
    {
        return new ChecklistQuestion([
            'answer_type' => 'yes_no',
            'check_point' => 'Sample checkpoint',
        ]);
    }
}
