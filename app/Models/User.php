<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'mysql_portal';
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'access_checklist' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // =========================================================
    // JEMBATAN KE DATABASE LOKAL
    // =========================================================

    /**
     * Relasi ke tabel users lokal menggunakan EMAIL sebagai jembatan.
     */
    public function localProfile()
    {
        return $this->hasOne(LocalUser::class, 'id', 'id');
    }

    // =================================================================
    // JEMBATAN SPATIE (BRIDGE)
    // Agar $user->can() atau $user->hasRole() tetap jalan
    // =================================================================

    /**
     * Override method 'can' bawaan Laravel (Gate).
     * Lempar pengecekan ke LocalUser.
     */
    public function can($abilities, $arguments = [])
    {
        return $this->localProfile ? $this->localProfile->can($abilities, $arguments) : false;
    }

    /**
     * Jembatan untuk cek Role
     */
    public function hasRole($roles, $guard = null)
    {
        return $this->localProfile ? $this->localProfile->hasRole($roles, $guard) : false;
    }

    public function hasAnyRole(...$roles)
    {
        if ($this->localProfile) {
            return $this->localProfile->hasAnyRole(...$roles);
        }
        return false;
    }

    public function getRoleNames()
    {
        if ($this->localProfile) {
            return $this->localProfile->getRoleNames();
        }
        return collect([]); // Return koleksi kosong agar tidak error
    }

    /**
     * Jembatan untuk cek Permission
     */
    // public function hasPermissionTo($permission, $guard = null)
    // {
    //     return $this->localProfile ? $this->localProfile->hasPermissionTo($permission, $guard) : false;
    // }
    public function hasPermissionTo($permission, $guard = null)
    {
        if (!$this->localProfile) {
            return false;
        }

        try {
            // Coba tanya ke Spatie di LocalUser
            return $this->localProfile->hasPermissionTo($permission, $guard);
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            // JIKA ERROR: Permission tidak ada di database (misal: 'is-super-admin' adalah Gate, bukan Permission)
            // Maka kita kembalikan FALSE (User tidak punya permission itu), bukannya Crash.
            return false;
        }
    }

    /**
     * Jembatan untuk Assign Role (Penting untuk User Management)
     */
    public function assignRole(...$roles)
    {
        if ($this->localProfile) {
            return $this->localProfile->assignRole(...$roles);
        }
        return $this;
    }

    /**
     * Jembatan untuk Remove Role
     */
    public function removeRole($role)
    {
        if ($this->localProfile) {
            return $this->localProfile->removeRole($role);
        }
        return $this;
    }

    /**
     * Jembatan untuk Sync Roles
     */
    public function syncRoles(...$roles)
    {
        if ($this->localProfile) {
            return $this->localProfile->syncRoles(...$roles);
        }
        return $this;
    }

    // =========================================================
    // HELPER FUNCTIONS
    // =========================================================

    public function isSuperAdmin()
    {
        return $this->role === 'Super Admin';
    }

    public function isRestaurantManager()
    {
        return $this->role === 'Restaurant Manager';
    }

    public function isAssRestaurantManager()
    {
        return $this->role === 'Assistant Restaurant Manager';
    }

    public function isFnBSupervisor()
    {
        return $this->role === 'F&B Supervisor';
    }

    public function isWaiter()
    {
        return $this->role === 'Waiter';
    }

    public function isCashier()
    {
        return $this->role === 'Cashier';
    }

    public function isBartender()
    {
        return $this->role === 'Bartender';
    }

    public function isDailyWorker()
    {
        return $this->role === 'Daily Worker';
    }

    public function isTrainee()
    {
        return $this->role === 'Trainee';
    }

    public function getRestaurantsAttribute()
    {
        return $this->localProfile ? $this->localProfile->restaurants : collect([]);
    }

    // public function restaurants()
    // {
    //     return $this->belongsToMany(Restaurant::class, 'restaurant_user')->withTimestamps();
    // }
}
