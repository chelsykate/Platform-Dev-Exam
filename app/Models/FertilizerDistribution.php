<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FertilizerDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_code',
        'request_id',
        'farmer_id',
        'fertilizer_id',
        'quantity',
        'distribution_date',
        'released_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'distribution_date' => 'datetime',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(FertilizerRequest::class, 'request_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function fertilizer(): BelongsTo
    {
        return $this->belongsTo(Fertilizer::class);
    }
}
