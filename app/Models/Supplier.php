<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'status',
    ];

    /**
     * Items supplied by this supplier.
     */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
