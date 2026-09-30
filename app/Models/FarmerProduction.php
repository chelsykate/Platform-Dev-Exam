<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerProduction extends Model
{
    use HasFactory;

    protected $table = 'farmer_production';

    protected $fillable = [
        'farmer_id',
        'farm_id',
        'crop_year',
        'planting_date',
        'harvest_date',
        'estimated_yield',
        'actual_yield',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'planting_date' => 'date',
            'harvest_date' => 'date',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }
}

