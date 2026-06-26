<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'date', 'start_time', 'end_time', 'total_hours',
        'reason', 'status', 'approved_by_manager', 'approved_by_hrd',
        'manager_approved_at', 'hrd_approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'total_hours' => 'decimal:2',
        'manager_approved_at' => 'datetime',
        'hrd_approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedByManager()
    {
        return $this->belongsTo(User::class, 'approved_by_manager');
    }

    public function approvedByHrd()
    {
        return $this->belongsTo(User::class, 'approved_by_hrd');
    }
}
