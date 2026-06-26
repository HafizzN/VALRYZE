<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyDocument::with('uploadedBy');
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        $documents = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'    => 'required|in:sop,regulation,sk,contract,other',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|file|mimes:pdf,doc,docx,xlsx,xls|max:20480',
            'is_public'   => 'nullable|boolean',
        ]);

        $file     = $request->file('file');
        $filePath = $file->store('company-documents', 'public');

        CompanyDocument::create([
            'user_id'      => Auth::id(),
            'category'     => $request->category,
            'title'        => $request->title,
            'description'  => $request->description,
            'file_path'    => $filePath,
            'file_name'    => $file->getClientOriginalName(),
            'mime_type'    => $file->getMimeType(),
            'file_size'    => $file->getSize(),
            'is_public'    => $request->boolean('is_public', true),
        ]);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    public function show(CompanyDocument $document)
    {
        return view('documents.show', compact('document'));
    }

    public function download(CompanyDocument $document)
    {
        $document->increment('download_count');
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(CompanyDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }
}
