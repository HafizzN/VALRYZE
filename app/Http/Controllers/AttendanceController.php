<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\OfficeLocation;
use App\Services\GeoFencingService;
use App\Services\WatermarkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    public function __construct(
        private GeoFencingService $geoFencing,
        private WatermarkService $watermark,
    ) {}

    /** Find the active attendance record for a user (today or yesterday, not checked out yet) */
    private function getActiveAttendance($user)
    {
        return Attendance::where('user_id', $user->id)
            ->whereIn('date', [Carbon::today(), Carbon::yesterday()])
            ->whereNull('check_out_time')
            ->first();
    }

    /** Show check-in page */
    public function checkInForm()
    {
        $user = Auth::user();
        $activeAttendance = $this->getActiveAttendance($user);

        if ($activeAttendance) {
            return redirect()->route('attendance.check-out')
                ->with('info', 'Anda sudah melakukan absen masuk untuk shift ' . ($activeAttendance->shift->name ?? '') . ' pukul ' . $activeAttendance->check_in_time . '. Silakan lakukan absen pulang.');
        }

        // Find completed shifts today
        $completedShiftIds = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->whereNotNull('check_out_time')
            ->pluck('shift_id')
            ->toArray();

        $officeLocations = OfficeLocation::where('is_active', true)->get();
        
        // Only show active shifts that haven't been completed today
        $shifts = \App\Models\Shift::where('is_active', true)
            ->whereNotIn('id', $completedShiftIds)
            ->get();

        if ($shifts->isEmpty()) {
            return redirect()->route('attendance.history')
                ->with('info', 'Anda sudah menyelesaikan semua sesi absensi shift yang tersedia untuk hari ini.');
        }

        return view('attendance.check-in', compact('officeLocations', 'shifts'));
    }

    /** Process check-in submission */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'photo'     => 'required|string', // base64
            'shift_id'  => 'required|exists:shifts,id',
        ]);

        $user      = Auth::user();
        $now       = $this->getSecureTime();
        $lat       = (float) $request->latitude;
        $lng       = (float) $request->longitude;
        $accuracy  = (float) $request->accuracy;

        // Block if already has an active session
        $activeAttendance = $this->getActiveAttendance($user);
        if ($activeAttendance) {
            return redirect()->route('attendance.check-out')
                ->with('error', 'Anda sudah melakukan absen masuk dan harus melakukan absen pulang terlebih dahulu.');
        }

        // Fetch selected shift
        $shift = \App\Models\Shift::findOrFail($request->shift_id);

        // Resolve active shift session
        $sessionTodayStart = Carbon::today()->setTimeFromTimeString($shift->start_time);
        $sessionTodayEnd = Carbon::today()->setTimeFromTimeString($shift->end_time);
        if ($shift->is_overnight) {
            $sessionTodayEnd->addDay();
        }
        $sessionTodayEarliest = $sessionTodayStart->copy()->subMinutes(60);

        $sessionYesterdayStart = Carbon::yesterday()->setTimeFromTimeString($shift->start_time);
        $sessionYesterdayEnd = Carbon::yesterday()->setTimeFromTimeString($shift->end_time);
        if ($shift->is_overnight) {
            $sessionYesterdayEnd->addDay();
        }
        $sessionYesterdayEarliest = $sessionYesterdayStart->copy()->subMinutes(60);

        $selectedSession = null;

        if ($now->gte($sessionTodayEarliest) && $now->lte($sessionTodayEnd)) {
            $selectedSession = [
                'date' => Carbon::today(),
                'start' => $sessionTodayStart,
                'end' => $sessionTodayEnd,
            ];
        } elseif ($now->gte($sessionYesterdayEarliest) && $now->lte($sessionYesterdayEnd)) {
            $selectedSession = [
                'date' => Carbon::yesterday(),
                'start' => $sessionYesterdayStart,
                'end' => $sessionYesterdayEnd,
            ];
        }

        if (!$selectedSession) {
            return back()->with('error', '❌ Absen masuk ditolak. Anda berada di luar jam absensi untuk shift ' . $shift->name . ' (Waktu absensi dibuka mulai dari 60 menit sebelum shift dimulai hingga shift berakhir).');
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
            return redirect()->route('attendance.history')
                ->with('info', 'Anda sudah menyelesaikan absensi untuk shift ' . $shift->name . ' pada tanggal ' . $sessionDate->format('d/m/Y') . '.');
        }

        // Fake GPS detection
        $isFakeGps = $this->geoFencing->detectFakeGps($lat, $lng, $accuracy);
        if ($isFakeGps) {
            return back()->with('error', '⚠️ Terdeteksi penggunaan GPS palsu (Fake GPS). Absensi ditolak!');
        }

        // Validate against office locations
        $geoResult = $this->geoFencing->validateAgainstOffices($lat, $lng);

        if (!$geoResult) {
            return back()->with('error', 'Tidak ada lokasi kantor yang terdaftar. Hubungi Admin.');
        }

        if (!$geoResult['within_radius']) {
            $distance = $geoResult['distance'];
            $radius   = $geoResult['office']->radius_meters;
            return back()->with('error', "❌ Lokasi Anda terlalu jauh dari kantor. Jarak: {$distance}m, Radius diizinkan: {$radius}m");
        }

        // Save photo from base64
        $photoPath = $this->saveBase64Photo($request->photo, 'attendance/check-in');

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

        // Determine late status using 10 minutes default tolerance if null
        $status = 'present';
        $lateMinutes = 0;

        $tolerance  = $shift->late_tolerance_minutes ?? 10; // Forced to 10 minutes default
        $deadline   = $scheduledStart->copy()->addMinutes($tolerance);

        if ($now->gt($deadline)) {
            $status      = 'late';
            $lateMinutes = $now->diffInMinutes($scheduledStart);
        }

        // Create or update attendance record for the resolved session date and shift_id
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
            ? "⚠️ Absen masuk berhasil, namun Anda terlambat {$lateMinutes} menit."
            : '✅ Absen masuk berhasil! Selamat bekerja.';

        return redirect()->route('dashboard')->with('success', $message);
    }

    /** Show check-out page */
    public function checkOutForm()
    {
        $user = Auth::user();
        $attendance = $this->getActiveAttendance($user);

        if (!$attendance || !$attendance->check_in_time) {
            return redirect()->route('attendance.check-in')
                ->with('error', 'Anda belum melakukan absen masuk.');
        }

        $officeLocations = OfficeLocation::where('is_active', true)->get();
        return view('attendance.check-out', compact('attendance', 'officeLocations'));
    }

    /** Process check-out submission */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'photo'     => 'required|string',
        ]);

        $user  = Auth::user();
        $lat   = (float) $request->latitude;
        $lng   = (float) $request->longitude;

        $attendance = $this->getActiveAttendance($user);
        if (!$attendance) {
            return redirect()->route('attendance.check-in')
                ->with('error', 'Sesi absensi aktif tidak ditemukan. Silakan lakukan absen masuk terlebih dahulu.');
        }

        $isFakeGps = $this->geoFencing->detectFakeGps($lat, $lng, (float) $request->accuracy);
        if ($isFakeGps) {
            return back()->with('error', '⚠️ Terdeteksi GPS palsu. Absen pulang ditolak!');
        }

        $geoResult = $this->geoFencing->validateAgainstOffices($lat, $lng);
        if (!$geoResult || !$geoResult['within_radius']) {
            $distance = $geoResult['distance'] ?? 'N/A';
            return back()->with('error', "❌ Lokasi tidak valid. Jarak dari kantor: {$distance}m");
        }

        $photoPath = $this->saveBase64Photo($request->photo, 'attendance/check-out');
        $now = $this->getSecureTime();

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

        // Calculate early check-out and strictly block if before shift end (minus tolerance)
        $shift = $attendance->shift;
        if ($shift) {
            // Construct the scheduled shift end datetime based on the check-in date
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
                return back()->with('error', "❌ Absen pulang ditolak. Anda tidak diperbolehkan melakukan absen pulang sebelum waktu shift berakhir. Absen pulang untuk shift " . $shift->name . " paling awal dibuka pukul " . $earliestAllowedCheckOut . " WIB.");
            }

            // Calculate early out minutes if checking out before scheduled shift end
            $earlyOutMinutes = 0;
            if ($now->lt($shiftEndDatetime)) {
                $earlyOutMinutes = $shiftEndDatetime->diffInMinutes($now);
            }
        } else {
            $earlyOutMinutes = 0;
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

        $message = '✅ Absen pulang berhasil! Sampai jumpa besok.';

        return redirect()->route('dashboard')->with('success', $message);
    }

    /** Attendance history */
    public function history(Request $request)
    {
        $user  = Auth::user();
        $query = $user->hasRole(['super_admin', 'hrd', 'manager'])
            ? Attendance::with(['user.division', 'user.position'])
            : Attendance::with(['user.division', 'user.position'])->where('user_id', $user->id);

        // Filters
        if ($request->filled('user_id') && $user->hasRole(['super_admin', 'hrd'])) {
            $query->where('user_id', $request->user_id);
        }

        $monthStr = $request->get('month', now()->format('Y-m'));
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        } else {
            $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Handle Export before pagination
        if ($request->get('export') === 'excel') {
            $exportData = $query->orderBy('date', 'desc')->get();
            return Excel::download(
                new AttendanceExport($exportData),
                "riwayat-absensi-" . now()->format('Y-m-d') . ".xlsx"
            );
        }

        if ($request->get('export') === 'pdf') {
            $exportData = $query->orderBy('date', 'desc')->get();
            $pdf = Pdf::loadView('reports.attendance-pdf', [
                'attendances' => $exportData,
                'month'       => $monthStr
            ])->setPaper('a4', 'landscape');
            return $pdf->download("riwayat-absensi-" . now()->format('Y-m-d') . ".pdf");
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        $users = $user->hasRole(['super_admin', 'hrd'])
            ? \App\Models\User::where('status', 'active')->orderBy('name')->get()
            : collect();

        return view('attendance.history', compact('attendances', 'users'));
    }

    /** Get secure time from public API with fallback to server time */
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

    private function saveBase64Photo(string $base64, string $folder): string
    {
        $data    = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $decoded = base64_decode($data);
        $filename = $folder . '/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($filename, $decoded);
        return $filename;
    }
}
