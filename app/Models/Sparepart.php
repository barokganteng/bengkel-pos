<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'stock',
        'sale_price',
    ];

    /**
     * Get all of the sparepart's details.
     */
    public function details(): MorphMany
    {
        return $this->morphMany(ServiceDetail::class, 'itemable');
    }
}
