<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sso_id',
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
        ];
    }

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_user')->withTimestamps();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }

    public function isRestaurantManager()
    {
        return $this->hasRole('Restaurant Manager');
    }

    public function isAssRestaurantManager()
    {
        return $this->hasRole('Assistant Restaurant Manager');
    }

    public function isFnBSupervisor()
    {
        return $this->hasRole('F&B Supervisor');
    }

    public function isWaiter()
    {
        return $this->hasRole('Waiter');
    }

    public function isCashier()
    {
        return $this->hasRole('Cashier');
    }

    public function isBartender()
    {
        return $this->hasRole('Bartender');
    }

    public function isDailyWorker()
    {
        return $this->hasRole('Daily Worker');
    }

    public function isTrainee()
    {
        return $this->hasRole('Trainee');
    }
}
