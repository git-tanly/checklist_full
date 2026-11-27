<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistSessionWindow extends Model
{
    protected $fillable = [
        'restaurant_id',
        'session_type',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('restaurant_id');
    }

    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }
}
