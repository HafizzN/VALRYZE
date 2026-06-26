<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\SendEmailJob;

class LetterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Letter::with('user');
        if (!$user->hasRole(['super_admin', 'hrd'])) {
            $query->where('user_id', $user->id);
        }
        $letters = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        return view('letters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_type' => 'required|in:permission,leave,assignment,field_duty,work_certificate,other',
            'subject'     => 'required|string|max:255',
            'content'     => 'nullable|string',
            'file_path'   => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('letters', 'public');
        }

        $letterNumber = Letter::generateNumber($request->letter_type);

        Letter::create([
            'user_id'       => Auth::id(),
            'letter_type'   => $request->letter_type,
            'letter_number' => $letterNumber,
            'subject'       => $request->subject,
            'content'       => $request->content,
            'file_path'     => $filePath,
            'status'        => 'pending',
        ]);

        return redirect()->route('letters.index')->with('success', "Surat {$letterNumber} berhasil diajukan.");
    }

    public function show(Letter $letter)
    {
        return view('letters.show', compact('letter'));
    }

    public function approve(Letter $letter)
    {
        $letter->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        try {
            SendEmailJob::dispatch(
                $letter->user->email,
                "Pengajuan Surat Disetujui",
                "Halo {$letter->user->name},\n\nKabar baik! Pengajuan surat Anda dengan nomor {$letter->letter_number} ({$letter->letter_type_name}) telah disetujui.\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Surat berhasil disetujui.');
    }

    public function reject(Request $request, Letter $letter)
    {
        $request->validate(['notes' => 'required|string']);
        $letter->update(['status' => 'rejected', 'notes' => $request->notes]);

        try {
            SendEmailJob::dispatch(
                $letter->user->email,
                "Pengajuan Surat Ditolak",
                "Halo {$letter->user->name},\n\nPengajuan surat Anda dengan nomor {$letter->letter_number} ({$letter->letter_type_name}) telah ditolak.\n\nAlasan penolakan: {$request->notes}\n\nSalam,\nSmart HR Portal"
            );
        } catch (\Exception $e) {
            // Ignore
        }

        return back()->with('success', 'Surat berhasil ditolak.');
    }

    public function download(Letter $letter)
    {
        // Generate PDF from content if no file uploaded
        if (!$letter->file_path) {
            $pdf = Pdf::loadView('letters.pdf', compact('letter'));
            return $pdf->download("surat-{$letter->letter_number}.pdf");
        }

        return response()->download(storage_path('app/public/' . $letter->file_path));
    }
}
