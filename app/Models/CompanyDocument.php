<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category', 'title', 'description',
        'file_path', 'file_name', 'mime_type', 'file_size',
        'download_count', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCategoryNameAttribute(): string
    {
        return match($this->category) {
            'sop'        => 'SOP',
            'regulation' => 'Peraturan',
            'sk'         => 'SK',
            'contract'   => 'Kontrak',
            default      => 'Lainnya',
        };
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
