<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueTarget extends Model
{
    //
    protected $fillable = [
        'restaurant_id',
        'year',
        'month',
        'budget_amount',
        'amount',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
