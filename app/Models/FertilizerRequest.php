<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FertilizerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'farmer_id',
        'fertilizer_id',
        'requested_quantity',
        'approved_quantity',
        'request_date',
        'approval_date',
        'approved_by',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'datetime',
            'approval_date' => 'datetime',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function fertilizer(): BelongsTo
    {
        return $this->belongsTo(Fertilizer::class);
    }

    public function distribution(): HasOne
    {
        return $this->hasOne(FertilizerDistribution::class, 'request_id');
    }
}
