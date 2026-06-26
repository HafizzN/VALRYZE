<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'title', 'file_path', 'file_name', 'mime_type', 'file_size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'ktp'       => 'KTP',
            'npwp'      => 'NPWP',
            'cv'        => 'CV / Resume',
            'contract'  => 'Kontrak Kerja',
            'bpjs_kes'  => 'BPJS Kesehatan',
            'bpjs_naker'=> 'BPJS Ketenagakerjaan',
            'ijazah'    => 'Ijazah',
            default     => 'Dokumen Lainnya',
        };
    }
}
