<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LocalUser;

class Restaurant extends Model
{
    protected $connection = 'mysql';
    protected $tabel = 'restaurants';
    //
    protected $fillable = [
        'code',
        'name',
    ];

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function upsellingItems()
    {
        return $this->hasMany(UpsellingItem::class);
    }

    public function users()
    {
        // return $this->belongsToMany(LocalUser::class, 'restaurant_user')->withTimestamps();
        return $this->belongsToMany(LocalUser::class, 'restaurant_user', 'restaurant_id', 'user_id');
    }
}
