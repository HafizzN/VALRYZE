<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function company()
    {
        $company = Company::first() ?? new Company();
        return view('settings.company', compact('company'));
    }

    public function updateCompany(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email',
            'website' => 'nullable|url',
            'logo'    => 'nullable|image|max:2048',
        ]);

        $company = Company::firstOrCreate([]);
        $data = $request->except(['logo', '_token', '_method']);

        if ($request->hasFile('logo')) {
            if ($company->logo) Storage::disk('public')->delete($company->logo);
            $data['logo'] = $request->file('logo')->store('company', 'public');
        }

        $company->update($data);
        return redirect()->route('settings.company')->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    public function auditLogs(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('model_type', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        return view('settings.audit_logs', compact('logs'));
    }
}
