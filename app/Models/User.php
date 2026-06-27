<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password',
        'nik', 'phone', 'address', 'photo', 'gender',
        'birth_date', 'birth_place', 'religion', 'marital_status',
        'division_id', 'position_id', 'shift_id',
        'join_date', 'resign_date', 'employment_type', 'status',
        'annual_leave_quota', 'annual_leave_used', 'api_token', 'fcm_token',
    ];

    protected $hidden = ['password', 'remember_token', 'api_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'join_date' => 'date',
            'resign_date' => 'date',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function permissionRequests()
    {
        return $this->hasMany(PermissionRequest::class);
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function todayAttendance()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }

    public function getRemainingLeaveAttribute(): int
    {
        return max(0, $this->annual_leave_quota - $this->annual_leave_used);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'data:image/')) {
                return $this->photo;
            }
            $baseUrl = request() ? request()->getSchemeAndHttpHost() : config('app.url');
            return $baseUrl . '/storage/' . $this->photo;
        }
        return null;
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper($words[0][0] . $words[1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function getRoleLabelAttribute(): string
    {
        $role = $this->roles->first();
        return $role ? match($role->name) {
            'super_admin' => 'Super Admin',
            'hrd'         => 'HRD',
            'manager'     => 'Manager',
            'karyawan'    => 'Karyawan',
            default       => ucfirst($role->name),
        } : 'Karyawan';
    }
}
