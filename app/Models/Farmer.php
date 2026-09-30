<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_code',
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'email',
        'address',
        'barangay',
        'municipality',
        'province',
        'status',
        'user_id',
    ];

    /**
     * Get full name helper.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} " . ($this->middle_name ? "{$this->middle_name} " : "") . $this->last_name);
    }

    /**
     * User account relation if linked.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Farms owned by farmer.
     */
    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class);
    }

    /**
     * Production history.
     */
    public function productions(): HasMany
    {
        return $this->hasMany(FarmerProduction::class);
    }

    /**
     * Farmer activities.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(FarmerActivity::class)->latest('activity_date');
    }
}
