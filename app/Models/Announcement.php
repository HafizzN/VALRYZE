<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'content', 'category',
        'is_pinned', 'attachment', 'published_at', 'expired_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
        })->where('published_at', '<=', now());
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function getCategoryNameAttribute(): string
    {
        return match($this->category) {
            'info'     => 'Informasi',
            'meeting'  => 'Rapat',
            'holiday'  => 'Hari Libur',
            'activity' => 'Kegiatan',
            default    => 'Lainnya',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'info'     => 'blue',
            'meeting'  => 'purple',
            'holiday'  => 'green',
            'activity' => 'orange',
            default    => 'gray',
        };
    }
}
