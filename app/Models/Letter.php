<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Letter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'letter_type', 'letter_number', 'subject',
        'content', 'file_path', 'status', 'approved_by', 'approved_at', 'notes',
    ];

    protected $casts = [
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

    public function getLetterTypeNameAttribute(): string
    {
        return match($this->letter_type) {
            'permission'        => 'Surat Izin',
            'leave'             => 'Surat Cuti',
            'assignment'        => 'Surat Tugas',
            'field_duty'        => 'Surat Dinas',
            'work_certificate'  => 'Surat Keterangan Kerja',
            default             => 'Surat Lainnya',
        };
    }

    public static function generateNumber(string $type): string
    {
        $prefix = match($type) {
            'permission'       => 'SI',
            'leave'            => 'SC',
            'assignment'       => 'ST',
            'field_duty'       => 'SD',
            'work_certificate' => 'SKK',
            default            => 'SL',
        };
        $month = now()->format('m');
        $year = now()->format('Y');
        $count = static::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('letter_type', $type)
            ->count() + 1;

        return sprintf('%s/%s/%s/%04d', $prefix, $month, $year, $count);
    }
}
