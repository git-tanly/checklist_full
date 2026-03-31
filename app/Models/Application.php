<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    // KONEKSI KE DATABASE PORTAL (SSO)
    protected $connection = 'mysql_portal';
    protected $table = 'applications';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'url',
        'icon',
        'color',
        'is_active'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
