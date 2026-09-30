<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'record_id',
        'old_value',
        'new_value',
    ];

    /**
     * User who triggered the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log system actions.
     */
    public static function log(string $action, string $module, ?int $recordId = null, $oldValue = null, $newValue = null): self
    {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_value' => is_array($oldValue) || is_object($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) || is_object($newValue) ? json_encode($newValue) : $newValue,
        ]);
    }
}
