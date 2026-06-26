<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'leave_type', 'start_date', 'end_date', 'total_days',
        'reason', 'attachment', 'child_name', 'child_birth_date',
        'wedding_date', 'status',
        'approved_by_manager', 'approved_by_hrd',
        'manager_approved_at', 'hrd_approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'child_birth_date' => 'date',
        'wedding_date' => 'date',
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

    public function getLeaveTypeNameAttribute(): string
    {
        return match($this->leave_type) {
            'annual'    => 'Cuti Tahunan',
            'maternity' => 'Cuti Melahirkan',
            'paternity' => 'Cuti Ayah',
            'wedding'   => 'Cuti Menikah',
            'big_leave' => 'Cuti Besar',
            'sick'      => 'Cuti Sakit',
            'other'     => 'Cuti Lainnya',
            default     => $this->leave_type,
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'pending'          => 'Menunggu',
            'approved_manager' => 'Disetujui Manager',
            'approved'         => 'Disetujui',
            'rejected'         => 'Ditolak',
            default            => $this->status,
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
