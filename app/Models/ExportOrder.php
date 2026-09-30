<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_code',
        'customer_id',
        'order_date',
        'destination',
        'total_amount',
        'shipment_date',
        'delivery_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'datetime',
            'shipment_date' => 'datetime',
            'delivery_date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
