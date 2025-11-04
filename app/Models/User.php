<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    /**
     * Scope a query to only include customers.
     */
    public function scopeCustomer(Builder $query): void
    {
        $query->where('role', 'pelanggan');
    }

    /**
     * Scope a query to only include mechanics.
     */
    public function scopeMechanic(Builder $query): void
    {
        $query->where('role', 'mekanik');
    }

    /**
     * Get all of the vehicles for the User (Customer).
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get all of the service histories for the User (as Customer).
     */
    public function serviceHistories(): HasMany
    {
        return $this->hasMany(ServiceHistory::class, 'customer_id');
    }

    /**
     * Get all of the assigned services for the User (as Mechanic).
     */
    public function assignedServices(): HasMany
    {
        return $this->hasMany(ServiceHistory::class, 'mechanic_id');
    }

    /**
     * Get all of the bookings for the User (as Customer).
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }
}
