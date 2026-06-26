<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'permission_type', 'date', 'end_date',
        'start_time', 'end_time', 'reason', 'attachment',
        'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getPermissionTypeNameAttribute(): string
    {
        return match($this->permission_type) {
            'sick'       => 'Sakit',
            'family'     => 'Keperluan Keluarga',
            'field_duty' => 'Dinas Luar',
            'personal'   => 'Izin Pribadi',
            default      => $this->permission_type,
        };
    }
}
