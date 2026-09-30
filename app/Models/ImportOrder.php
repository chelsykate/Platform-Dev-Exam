<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_code',
        'supplier_id',
        'order_date',
        'expected_arrival',
        'actual_arrival',
        'total_cost',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'datetime',
            'expected_arrival' => 'datetime',
            'actual_arrival' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
