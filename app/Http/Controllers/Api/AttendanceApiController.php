<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\OfficeLocation;
use App\Models\User;
use App\Services\GeoFencingService;
use App\Services\WatermarkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceApiController extends Controller
{
    public function __construct(
        private GeoFencingService $geoFencing,
        private WatermarkService $watermark,
    ) {}

    /**
     * Authenticate employee and generate API token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi salah.'
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Hubungi HRD.'
            ], 403);
        }

        // Generate secure API token
        $token = Str::random(80);
        $user->update([
            'api_token' => $token
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nik' => $user->nik,
                'role' => $user->role_label,
                'role_name' => $user->roles->first()->name ?? 'karyawan',
                'division' => $user->division->name ?? '-',
                'position' => $user->position->name ?? '-',
                'shift' => $user->shift ? [
                    'id' => $user->shift->id,
                    'name' => $user->shift->name,
                    'start_time' => Carbon::parse($user->shift->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($user->shift->end_time)->format('H:i'),
                ] : null,
            ]
        ]);
    }

    /**
     * Get authenticated user profile and details.
     */
    public function profile()
    {
        $user = Auth::user();

        // Load active attendance (if checked in but not checked out today/yesterday)
        $activeAttendance = Attendance::where('user_id', $user->id)
            ->whereIn('date', [Carbon::today('Asia/Jakarta'), Carbon::yesterday('Asia/Jakarta')])
            ->whereNull('check_out_time')
            ->first();

        // Get office locations for geofencing reference on mobile
        $officeLocations = OfficeLocation::where('is_active', true)->get()->map(function ($loc) {
            return [
                'id' => $loc->id,
                'name' => $loc->name,
                'latitude' => (float) $loc->latitude,
                'longitude' => (float) $loc->longitude,
                'radius' => (int) $loc->radius_meters,
            ];
        });

        // Find completed shifts today
        $completedShiftIds = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today('Asia/Jakarta'))
            ->whereNotNull('check_out_time')
            ->pluck('shift_id')
            ->toArray();

        // Get active shifts not completed today
        $shifts = \App\Models\Shift::where('is_active', true)
            ->whereNotIn('id', $completedShiftIds)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'start_time' => Carbon::parse($s->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($s->end_time)->format('H:i'),
                    'is_overnight' => (bool) $s->is_overnight,
                ];
            });

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nik' => $user->nik,
                'email' => $user->email,
                'phone' => $user->phone ?? '-',
                'photo_url' => $user->photo_url,
                'division' => $user->division->name ?? '-',
                'position' => $user->position->name ?? '-',
                'shift_id' => $user->shift_id,
                'role_name' => $user->roles->first()->name ?? 'karyawan',
            ],
            'active_attendance' => $activeAttendance ? [
                'id' => $activeAttendance->id,
                'date' => $activeAttendance->date,
                'check_in_time' => $activeAttendance->check_in_time,
                'shift_name' => $activeAttendance->shift->name ?? '',
            ] : null,
            'office_locations' => $officeLocations,
            'shifts' => $shifts,
        ]);
    }

    /**
     * Process check-in from mobile app.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'photo'     => 'required|file|image|max:10240',
            'shift_id'  => 'required|exists:shifts,id',
            'address'   => 'nullable|string',
        ]);

        $user      = Auth::user();
        $now       = $this->getSecureTime();
        $lat       = (float) $request->latitude;
        $lng       = (float) $request->longitude;
        $accuracy  = (float) $request->accuracy;

        // Block if already has an active session
        $activeAttendance = Attendance::where('user_id', $user->id)
            ->whereIn('date', [Carbon::today('Asia/Jakarta'), Carbon::yesterday('Asia/Jakarta')])
            ->whereNull('check_out_time')
            ->first();
        if ($activeAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk dan harus melakukan absen pulang terlebih dahulu.'
            ], 400);
        }

        // Fetch selected shift
        $shift = \App\Models\Shift::findOrFail($request->shift_id);

        // Resolve active shift session
        $sessionTodayStart = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($shift->start_time);
        $sessionTodayEnd = Carbon::today('Asia/Jakarta')->setTimeFromTimeString($shift->end_time);
        if ($shift->is_overnight) {
            $sessionTodayEnd->addDay();
        }
        $sessionTodayEarliest = $sessionTodayStart->copy()->subMinutes(60);

        $sessionYesterdayStart = Carbon::yesterday('Asia/Jakarta')->setTimeFromTimeString($shift->start_time);
        $sessionYesterdayEnd = Carbon::yesterday('Asia/Jakarta')->setTimeFromTimeString($shift->end_time);
        if ($shift->is_overnight) {
            $sessionYesterdayEnd->addDay();
        }
        $sessionYesterdayEarliest = $sessionYesterdayStart->copy()->subMinutes(60);

        $selectedSession = null;

        if ($now->gte($sessionTodayEarliest) && $now->lte($sessionTodayEnd)) {
            $selectedSession = [
                'date' => Carbon::today('Asia/Jakarta'),
                'start' => $sessionTodayStart,
                'end' => $sessionTodayEnd,
            ];
        } elseif ($now->gte($sessionYesterdayEarliest) && $now->lte($sessionYesterdayEnd)) {
            $selectedSession = [
                'date' => Carbon::yesterday('Asia/Jakarta'),
                'start' => $sessionYesterdayStart,
                'end' => $sessionYesterdayEnd,
            ];
        }

        if (!$selectedSession) {
            return response()->json([
                'success' => false,
                'message' => '❌ Absen masuk ditolak. Anda berada di luar jam absensi untuk shift ' . $shift->name . ' (Waktu absensi dibuka mulai dari 60 menit sebelum shift dimulai hingga shift berakhir).'
            ], 400);
        }

        $sessionDate = $selectedSession['date'];
        $scheduledStart = $selectedSession['start'];

        // Block if already completed this specific shift for the resolved session date
        $completedThisShift = Attendance::where('user_id', $user->id)
            ->where('shift_id', $shift->id)
            ->where('date', $sessionDate)
            ->whereNotNull('check_out_time')
            ->first();
        if ($completedThisShift) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah menyelesaikan absensi untuk shift ' . $shift->name . ' pada tanggal ' . $sessionDate->format('d/m/Y') . '.'
            ], 400);
        }

        // Fake GPS detection (at server level, in addition to client level)
        $isFakeGps = $this->geoFencing->detectFakeGps($lat, $lng, $accuracy);
        if ($isFakeGps) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Terdeteksi penggunaan GPS palsu (Fake GPS). Absensi ditolak!'
            ], 400);
        }

        // Validate against office locations
        $geoResult = $this->geoFencing->validateAgainstOffices($lat, $lng);
        if (!$geoResult) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada lokasi kantor yang terdaftar. Hubungi Admin.'
            ], 400);
        }

        if (!$geoResult['within_radius']) {
            $distance = $geoResult['distance'];
            $radius   = $geoResult['office']->radius_meters;
            return response()->json([
                'success' => false,
                'message' => "❌ Lokasi Anda terlalu jauh dari kantor. Jarak: {$distance}m, Radius diizinkan: {$radius}m"
            ], 400);
        }

        // Save photo from multipart file
        $file = $request->file('photo');
        $photoPath = $file->store('attendance/check-in', 'public');

        // Apply watermark
        $watermarkData = $this->watermark->buildAttendanceWatermarkData(
            $user->name,
            $now->format('d/m/Y'),
            $now->format('H:i:s'),
            $lat, $lng,
            $geoResult['distance']
        );

        $fullPath = Storage::disk('public')->path($photoPath);
        $watermarkedPath = $this->watermark->applyWatermark($fullPath, $watermarkData);
        $finalPhotoPath = str_replace(Storage::disk('public')->path(''), '', $watermarkedPath);

        // Determine late status
        $status = 'present';
        $lateMinutes = 0;

        $tolerance  = $shift->late_tolerance_minutes ?? 10;
        $deadline   = $scheduledStart->copy()->addMinutes($tolerance);

        if ($now->gt($deadline)) {
            $status      = 'late';
            $lateMinutes = $now->diffInMinutes($scheduledStart);
        }

        // Create or update attendance record using user_id, date, and shift_id
        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $sessionDate, 'shift_id' => $shift->id],
            [
                'office_location_id'   => $geoResult['office']->id,
                'check_in_time'        => $now->format('H:i:s'),
                'check_in_photo'       => $finalPhotoPath,
                'check_in_latitude'    => $lat,
                'check_in_longitude'   => $lng,
                'check_in_address'     => $request->address ?? '',
                'check_in_distance'    => $geoResult['distance'],
                'is_fake_gps'          => false,
                'status'               => $status,
                'late_minutes'         => $lateMinutes,
            ]
        );

        $message = $status === 'late'
            ? "Absen masuk berhasil, namun Anda terlambat {$lateMinutes} menit."
            : 'Absen masuk berhasil! Selamat bekerja.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $attendance
        ]);
    }

    /**
     * Process check-out from mobile app.
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'photo'     => 'required|file|image|max:10240',
            'address'   => 'nullable|string',
        ]);

        $user  = Auth::user();
        $lat   = (float) $request->latitude;
        $lng   = (float) $request->longitude;
        $accuracy = (float) $request->accuracy;

        // Find active session
        $attendance = Attendance::where('user_id', $user->id)
            ->whereIn('date', [Carbon::today('Asia/Jakarta'), Carbon::yesterday('Asia/Jakarta')])
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi absensi aktif tidak ditemukan. Silakan lakukan absen masuk terlebih dahulu.'
            ], 400);
        }

        // Server-level fake GPS detection
        $isFakeGps = $this->geoFencing->detectFakeGps($lat, $lng, $accuracy);
        if ($isFakeGps) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Terdeteksi GPS palsu. Absen pulang ditolak!'
            ], 400);
        }

        // Validate office radius
        $geoResult = $this->geoFencing->validateAgainstOffices($lat, $lng);
        if (!$geoResult || !$geoResult['within_radius']) {
            $distance = $geoResult['distance'] ?? 'N/A';
            return response()->json([
                'success' => false,
                'message' => "❌ Lokasi tidak valid. Jarak dari kantor: {$distance}m"
            ], 400);
        }

        $file = $request->file('photo');
        $photoPath = $file->store('attendance/check-out', 'public');
        $now = $this->getSecureTime();

        // Watermark data
        $watermarkData = $this->watermark->buildAttendanceWatermarkData(
            $user->name,
            $now->format('d/m/Y'),
            $now->format('H:i:s'),
            $lat, $lng,
            $geoResult['distance']
        );

        $fullPath = Storage::disk('public')->path($photoPath);
        $watermarkedPath = $this->watermark->applyWatermark($fullPath, $watermarkData);
        $finalPhotoPath = str_replace(Storage::disk('public')->path(''), '', $watermarkedPath);

        // Validate early out
        $shift = $attendance->shift;
        $earlyOutMinutes = 0;

        if ($shift) {
            $checkInDate = Carbon::parse($attendance->date);
            $shiftEndDatetime = Carbon::createFromFormat(
                'Y-m-d H:i:s', 
                $checkInDate->format('Y-m-d') . ' ' . $shift->end_time, 
                'Asia/Jakarta'
            );
            if ($shift->is_overnight) {
                $shiftEndDatetime->addDay();
            }

            $tolerance = $shift->early_out_tolerance_minutes ?? 0;
            $threshold = $shiftEndDatetime->copy()->subMinutes($tolerance);

            if ($now->lt($threshold)) {
                $earliestAllowedCheckOut = $threshold->format('H:i');
                return response()->json([
                    'success' => false,
                    'message' => "❌ Absen pulang ditolak. Anda tidak diperbolehkan melakukan absen pulang sebelum waktu shift berakhir. Paling awal dibuka pukul {$earliestAllowedCheckOut} WIB."
                ], 400);
            }

            if ($now->lt($shiftEndDatetime)) {
                $earlyOutMinutes = $shiftEndDatetime->diffInMinutes($now);
            }
        }

        $attendance->update([
            'check_out_time'     => $now->format('H:i:s'),
            'check_out_photo'    => $finalPhotoPath,
            'check_out_latitude' => $lat,
            'check_out_longitude'=> $lng,
            'check_out_address'  => $request->address ?? '',
            'check_out_distance' => $geoResult['distance'],
            'early_out_minutes'  => $earlyOutMinutes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil! Sampai jumpa besok.',
            'data' => $attendance
        ]);
    }

    /**
     * Get monthly attendance history for the authenticated employee.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $monthStr = $request->get('month', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $monthStr);

        $attendances = Attendance::with(['shift'])
            ->where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($att) {
                return [
                    'id' => $att->id,
                    'date' => $att->date,
                    'day_name' => Carbon::parse($att->date)->translatedFormat('l'),
                    'check_in_time' => $att->check_in_time ? Carbon::parse($att->check_in_time)->format('H:i') : null,
                    'check_out_time' => $att->check_out_time ? Carbon::parse($att->check_out_time)->format('H:i') : null,
                    'status' => $att->status,
                    'status_label' => match($att->status) {
                        'present' => 'Hadir',
                        'late' => 'Terlambat',
                        'absent' => 'Mangkir',
                        'leave' => 'Cuti',
                        'permission' => 'Izin',
                        'sick' => 'Sakit',
                        default => ucfirst($att->status),
                    },
                    'late_minutes' => $att->late_minutes,
                    'early_out_minutes' => $att->early_out_minutes,
                    'duration' => $att->work_duration ?? '-',
                    'check_in_photo_url' => $att->check_in_photo ? asset('storage/' . $att->check_in_photo) : null,
                    'check_out_photo_url' => $att->check_out_photo ? asset('storage/' . $att->check_out_photo) : null,
                    'check_in_address' => $att->check_in_address,
                    'check_out_address' => $att->check_out_address,
                    'check_in_distance' => $att->check_in_distance,
                    'check_out_distance' => $att->check_out_distance,
                ];
            });

        return response()->json([
            'success' => true,
            'month' => $monthStr,
            'history' => $attendances
        ]);
    }

    /**
     * Get secure time from public API with fallback to server time.
     */
    private function getSecureTime(): Carbon
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 1.5]);
            $response = $client->get('http://worldtimeapi.org/api/timezone/Asia/Jakarta');
            $data = json_decode($response->getBody()->getContents(), true);
            if (isset($data['datetime'])) {
                return Carbon::parse($data['datetime'])->timezone('Asia/Jakarta');
            }
        } catch (\Exception $e) {
            // Fallback to server time
        }
        return Carbon::now()->timezone('Asia/Jakarta');
    }

    /**
     * Export attendance history to Excel or PDF for mobile clients.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        $query = Attendance::with(['shift', 'user.division', 'user.position']);
        
        // HRD/Super Admin can export anyone, Manager can export their division, Karyawan can only export themselves
        if ($user->hasRole(['super_admin', 'hrd'])) {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } elseif ($user->hasRole('manager')) {
            $divisionUserIds = User::where('division_id', $user->division_id)->pluck('id');
            if ($request->filled('user_id') && $divisionUserIds->contains($request->user_id)) {
                $query->where('user_id', $request->user_id);
            } else {
                $query->whereIn('user_id', $divisionUserIds);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        $monthStr = $request->get('month', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $monthStr);
        $query->whereYear('date', $year)->whereMonth('date', $month);

        $exportData = $query->orderBy('date', 'desc')->get();

        if ($request->get('export') === 'excel') {
            return Excel::download(
                new AttendanceExport($exportData),
                "riwayat-absensi-" . $monthStr . "-" . now()->format('Y-m-d') . ".xlsx"
            );
        }

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.attendance-pdf', [
                'attendances' => $exportData,
                'month'       => $monthStr
            ])->setPaper('a4', 'landscape');
            return $pdf->download("riwayat-absensi-" . $monthStr . "-" . now()->format('Y-m-d') . ".pdf");
        }

        return response()->json([
            'success' => false,
            'message' => 'Format ekspor tidak didukung.'
        ], 400);
    }

    /**
     * Get list of notifications for the authenticated user.
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markNotificationRead($id)
    {
        $user = Auth::user();
        $notification = \App\Models\Notification::where('user_id', $user->id)->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai dibaca.'
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        \App\Models\Notification::where('user_id', $user->id)->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai dibaca.'
        ]);
    }

    /**
     * Update user profile photo.
     */
    public function updateProfilePhoto(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'photo' => 'required|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->photo);
            }

            // Save new photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->update(['photo' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui.',
                'photo_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File foto tidak ditemukan.'
        ], 400);
    }
}
