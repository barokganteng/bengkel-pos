<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    /**
     * Get all of the service's details.
     */
    public function details(): MorphMany
    {
        return $this->morphMany(ServiceDetail::class, 'itemable');
    }
}
