<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'item_code',
        'item_name',
        'category',
        'item_type',
        'quantity',
        'unit',
        'reorder_level',
        'unit_cost',
        'supplier_id',
        'status',
    ];

    /**
     * Check if low stock condition is met: quantity <= reorder_level.
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Supplier relationship.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Inventory transaction log history.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class)->latest('transaction_date');
    }
}
