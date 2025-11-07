<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_plate',
        'brand',
        'model',
        'year',
    ];

    /**
     * Get the user (owner) that owns the Vehicle.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the serviceHistories for the Vehicle.
     */
    public function serviceHistories(): HasMany
    {
        return $this->hasMany(ServiceHistory::class);
    }

    /**
     * Get the latest service history record for the vehicle.
     * Ini adalah relasi untuk mengambil HANYA 1 servis TERBARU.
     */
    public function latestService(): HasOne
    {
        return $this->hasOne(ServiceHistory::class)->latestOfMany('service_date');
    }
}
