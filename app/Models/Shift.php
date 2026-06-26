<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'start_time', 'end_time',
        'late_tolerance_minutes', 'early_out_tolerance_minutes', 'is_overnight', 'color', 'is_active',
    ];

    protected $casts = [
        'is_overnight' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }
}
