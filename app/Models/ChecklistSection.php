<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistSection extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ChecklistQuestion::class, 'section_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ChecklistSubmission::class, 'section_id');
    }
}
