<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ServiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_history_id',
        'itemable_id',
        'itemable_type',
        'quantity',
        'price_at_transaction',
    ];

    /**
     * Get the history that owns the ServiceDetail.
     */
    public function history(): BelongsTo
    {
        return $this->belongsTo(ServiceHistory::class);
    }

    /**
     * Get the parent itemable model (Service or Sparepart).
     */
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }
}
