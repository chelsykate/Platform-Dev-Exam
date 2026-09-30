<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'farm_name',
        'location',
        'farm_size',
        'farm_size_unit',
        'soil_type',
        'status',
    ];

    /**
     * Owning farmer.
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    /**
     * Production records for this farm.
     */
    public function productions(): HasMany
    {
        return $this->hasMany(FarmerProduction::class);
    }
}

