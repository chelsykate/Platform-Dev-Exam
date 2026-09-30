<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fertilizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'fertilizer_code',
        'name',
        'type',
        'unit',
        'description',
        'inventory_id',
        'status',
    ];

    /**
     * Linked inventory record.
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Requests for this fertilizer.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(FertilizerRequest::class);
    }

    /**
     * Distribution records for this fertilizer.
     */
    public function distributions(): HasMany
    {
        return $this->hasMany(FertilizerDistribution::class);
    }
}
