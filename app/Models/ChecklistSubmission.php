<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistSubmission extends Model
{
    protected $fillable = [
        'restaurant_id',
        'section_id',
        'employee_id',
        'session_type',
        'date',
        'status',
        'verified_by',
        'verified_at',
        'verification_note',
        'submitted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'verified_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ChecklistSection::class, 'section_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ChecklistAnswer::class, 'submission_id');
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
