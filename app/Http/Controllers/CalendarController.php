<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display the calendar and attendance records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        $selectedUserId = Auth::id();
        $canFilter = $user->hasRole(['super_admin', 'hrd', 'manager']);

        if ($canFilter && $request->filled('user_id')) {
            $reqUserId = (int) $request->user_id;
            
            // Manager can only view their own division's employees
            if ($user->hasRole('manager')) {
                $isDivisionMember = User::where('id', $reqUserId)
                    ->where('division_id', $user->division_id)
                    ->exists();
                if ($isDivisionMember) {
                    $selectedUserId = $reqUserId;
                }
            } else {
                $selectedUserId = $reqUserId;
            }
        }

        $selectedUser = User::findOrFail($selectedUserId);

        // Fetch attendance records for the selected employee in the selected month
        $attendances = Attendance::where('user_id', $selectedUserId)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->get()
            ->groupBy(fn($a) => $a->date->format('Y-m-d'));

        // Fetch holidays for the selected month
        $holidays = Holiday::whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'));

        // Fetch birthdays for the selected month
        $birthdays = User::where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', $monthNum)
            ->get()
            ->groupBy(fn($u) => $u->birth_date->format('d'));

        // Fetch employees list for dropdown if authorized
        $employees = collect();
        if ($user->hasRole(['super_admin', 'hrd'])) {
            $employees = User::where('status', 'active')->orderBy('name')->get();
        } elseif ($user->hasRole('manager')) {
            $employees = User::where('division_id', $user->division_id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        return view('calendar.index', compact('attendances', 'holidays', 'employees', 'selectedUser', 'month', 'canFilter', 'birthdays'));
    }

    /**
     * Store a new holiday and auto-generate attendance records for all active employees.
     */
    public function storeHoliday(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'date' => 'required|date|unique:holidays,date',
            'description' => 'nullable|string|max:255',
        ], [
            'date.unique' => 'Tanggal ini sudah didaftarkan sebagai hari libur.',
        ]);

        $holiday = Holiday::create([
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        // Auto-generate holiday attendance records for all active employees who don't have an attendance record today
        $activeEmployees = User::where('status', 'active')->get();
        
        foreach ($activeEmployees as $employee) {
            // We use updateOrCreate but only overwrite if it's not a workday attendance (present/late)
            $existing = Attendance::where('user_id', $employee->id)
                ->whereDate('date', $request->date)
                ->first();
                
            if (!$existing || in_array($existing->status, ['absent', 'holiday'])) {
                Attendance::updateOrCreate(
                    ['user_id' => $employee->id, 'date' => $request->date],
                    [
                        'status' => 'holiday',
                        'notes' => $request->name,
                        'shift_id' => null,
                    ]
                );
            }
        }

        // Log the activity
        ActivityLog::log(
            'create_holiday', 
            Holiday::class, 
            $holiday->id, 
            ['name' => $holiday->name, 'date' => $holiday->date->format('Y-m-d')]
        );

        return response()->json([
            'success' => true,
            'message' => 'Hari libur berhasil didaftarkan dan diterapkan ke absensi karyawan.',
        ]);
    }

    /**
     * Delete a holiday and clean up the auto-generated holiday attendance records.
     */
    public function destroyHoliday(Holiday $holiday)
    {
        $holidayDate = $holiday->date->format('Y-m-d');
        $holidayName = $holiday->name;
        $holidayId = $holiday->id;

        // Delete the holiday
        $holiday->delete();

        // Delete attendance records that were marked as holiday on this date
        Attendance::whereDate('date', $holidayDate)
            ->where('status', 'holiday')
            ->delete();

        // Log the activity
        ActivityLog::log(
            'delete_holiday', 
            Holiday::class, 
            $holidayId, 
            ['name' => $holidayName, 'date' => $holidayDate]
        );

        return response()->json([
            'success' => true,
            'message' => 'Hari libur berhasil dihapus dan rekap absensi dikembalikan ke semula.',
        ]);
    }
}
