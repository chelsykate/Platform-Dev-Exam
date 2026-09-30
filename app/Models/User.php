<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passkeys\PasskeyAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Accessor for role_name.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->name : 'Unassigned';
    }

    /**
     * Audit logs created by this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if user has a specific role by slug or name.
     */
    public function hasRole(string $role): bool
    {
        if (!$this->role) {
            return false;
        }

        return strtolower($this->role->slug) === strtolower($role) ||
               strtolower($this->role->name) === strtolower($role);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        if (!$this->role) {
            return false;
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('System Administrator');
    }

    public function isWarehouse(): bool
    {
        return $this->hasRole('warehouse') || $this->hasRole('Warehouse Staff');
    }

    public function isHR(): bool
    {
        return $this->hasRole('hr') || $this->hasRole('HR / Employee');
    }

    public function isSales(): bool
    {
        return $this->hasRole('sales') || $this->hasRole('Sales and Distribution Staff');
    }

    public function isFarmer(): bool
    {
        return $this->hasRole('farmer') || $this->hasRole('Farmer');
    }

    public function isFinance(): bool
    {
        return $this->hasRole('finance') || $this->hasRole('Finance');
    }

    public function isManagement(): bool
    {
        return $this->hasRole('management') || $this->hasRole('Management');
    }
}
