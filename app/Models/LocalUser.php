<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class LocalUser extends Model
{
    use HasRoles;

    // Koneksi ke database lokal (asset-management)
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $guard_name = 'web';

    public $incrementing = false;
    protected $keyType = 'int';

    // Kolom yang ingin kita ambil
    protected $fillable = [
        'id',
        'name',
        'email',
        'password'
    ];

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_user', 'user_id', 'restaurant_id');
    }
}
