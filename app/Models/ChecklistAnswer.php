<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistAnswer extends Model
{
    protected $fillable = [
        'submission_id',
        'question_id',
        'answer_number',
        'answer_boolean',
        'remarks',
        'result',
        'check_point',
        'key_check_point',
        'answer_type',
        'unit',
        'std_min',
        'std_max',
        'temp_max',
    ];

    protected $casts = [
        'answer_number' => 'decimal:2',
        'answer_boolean' => 'boolean',
        'remarks' => 'string',
        'result' => 'string',
        'std_min' => 'decimal:2',
        'std_max' => 'decimal:2',
        'temp_max' => 'decimal:2',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ChecklistSubmission::class, 'submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ChecklistQuestion::class, 'question_id');
    }
}
