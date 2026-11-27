<?php

namespace App\Services\Operational;

use App\Models\ChecklistQuestion;

class ChecklistAnswerEvaluator
{
    public const RESULT_OK = 'ok';

    public const RESULT_NOT_OK = 'not_ok';

    /**
     * Nilai jawaban terhadap batas standar pertanyaan.
     *
     * Urutan evaluasi:
     * 1. yes_no  : yes -> ok, no -> not_ok.
     * 2. temp_max: ok bila nilai <= temp_max.
     * 3. std_min / std_max: ok bila nilai di dalam rentang (inklusif).
     * 4. Tanpa batas: selalu ok.
     */
    public function evaluate(ChecklistQuestion $question, mixed $value): string
    {
        if ($value === null || $value === '') {
            return self::RESULT_NOT_OK;
        }

        if ($question->answer_type === 'yes_no') {
            return $this->toBoolean($value) ? self::RESULT_OK : self::RESULT_NOT_OK;
        }

        $number = (float) $value;

        if ($question->temp_max !== null) {
            return $number <= (float) $question->temp_max
                ? self::RESULT_OK
                : self::RESULT_NOT_OK;
        }

        $hasMin = $question->std_min !== null;
        $hasMax = $question->std_max !== null;

        if (! $hasMin && ! $hasMax) {
            return self::RESULT_OK;
        }

        if ($hasMin && $number < (float) $question->std_min) {
            return self::RESULT_NOT_OK;
        }

        if ($hasMax && $number > (float) $question->std_max) {
            return self::RESULT_NOT_OK;
        }

        return self::RESULT_OK;
    }

    /**
     * Normalisasi input yes/no dari form (string, int, atau bool).
     */
    private function toBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'yes', 'y', 'true', 'on'], true);
    }
}
