<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an admin activity.
     */
    public static function log(string $action, string $modelType, ?int $modelId, ?array $details = null): self
    {
        return static::create([
            'user_id'    => auth()->id() ?? 1, // Fallback to 1 (Super Admin) if no auth session
            'action'     => $action,
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'details'    => $details,
        ]);
    }
}
