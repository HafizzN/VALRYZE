<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('user')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category'    => 'required|in:info,meeting,holiday,activity,other',
            'is_pinned'   => 'nullable|boolean',
            'published_at'=> 'nullable|date',
            'expired_at'  => 'nullable|date|after:published_at',
            'attachment'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        Announcement::create([
            'user_id'      => Auth::id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'category'     => $request->category,
            'is_pinned'    => $request->boolean('is_pinned'),
            'attachment'   => $attachmentPath,
            'published_at' => $request->published_at ?? now(),
            'expired_at'   => $request->expired_at,
        ]);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Announcement $announcement)
    {
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|in:info,meeting,holiday,activity,other',
        ]);

        $announcement->update($request->only([
            'title', 'content', 'category', 'is_pinned', 'published_at', 'expired_at'
        ]) + ['is_pinned' => $request->boolean('is_pinned')]);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
