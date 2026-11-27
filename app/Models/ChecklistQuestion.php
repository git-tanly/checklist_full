<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistQuestion extends Model
{
    protected $fillable = [
        'section_id',
        'session_type',
        'check_point',
        'key_check_point',
        'answer_type',
        'std_min',
        'std_max',
        'temp_max',
        'unit',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'std_min' => 'decimal:2',
        'std_max' => 'decimal:2',
        'temp_max' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ChecklistSection::class, 'section_id');
    }

    public function scopeForSession(Builder $query, string $sessionType): Builder
    {
        return $query->where('session_type', $sessionType);
    }
}
