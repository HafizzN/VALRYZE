<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'office_location_id', 'shift_id', 'date',
        'check_in_time', 'check_out_time',
        'check_in_photo', 'check_out_photo',
        'check_in_latitude', 'check_in_longitude',
        'check_out_latitude', 'check_out_longitude',
        'check_in_address', 'check_out_address',
        'check_in_distance', 'check_out_distance',
        'is_fake_gps', 'status', 'late_minutes', 'early_out_minutes', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_fake_gps' => 'boolean',
        'check_in_latitude' => 'decimal:7',
        'check_in_longitude' => 'decimal:7',
        'check_out_latitude' => 'decimal:7',
        'check_out_longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function officeLocation()
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'present'    => '<span class="badge-success">Hadir</span>',
            'late'       => '<span class="badge-warning">Terlambat</span>',
            'absent'     => '<span class="badge-danger">Absen</span>',
            'permission' => '<span class="badge-info">Izin</span>',
            'leave'      => '<span class="badge-purple">Cuti</span>',
            'sick'       => '<span class="badge-orange">Sakit</span>',
            'holiday'    => '<span class="badge-gray">Libur</span>',
            default      => '<span class="badge-gray">-</span>',
        };
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }
}
