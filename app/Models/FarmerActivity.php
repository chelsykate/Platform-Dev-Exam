<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerActivity extends Model
{
    use HasFactory;

    protected $table = 'farmer_activity';

    protected $fillable = [
        'farmer_id',
        'activity_type',
        'description',
        'activity_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'datetime',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }
}

