<?php

namespace App\Http\Controllers;

use App\Models\ContentCalendar;
use App\Models\User;
use App\Services\AuditLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentCalendarController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['mentor', 'admin', 'owner'])->get();
        $platforms = ['Instagram', 'TikTok', 'YouTube', 'Facebook', 'LinkedIn', 'Twitter/X'];
        $statuses = [
            'draft' => 'Ide / Draft',
            'production' => 'Produksi',
            'review' => 'Review',
            'scheduled' => 'Scheduled',
            'published' => 'Published',
        ];

        return view('content_calendar.index', compact('users', 'platforms', 'statuses'));
    }

    public function events(Request $request)
    {
        $query = ContentCalendar::with('assignee', 'creator');

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->get();

        $colorMap = [
            'draft' => '#6b7280',     // Gray
            'production' => '#f59e0b',// Amber
            'review' => '#3b82f6',    // Blue
            'scheduled' => '#8b5cf6', // Purple
            'published' => '#10b981', // Green
        ];

        $events = $items->map(function ($item) use ($colorMap) {
            return [
                'id' => $item->id,
                'title' => "[{$item->platform}] {$item->title}",
                'start' => $item->scheduled_at->toIso8601String(),
                'backgroundColor' => $colorMap[$item->status] ?? '#3b82f6',
                'borderColor' => $colorMap[$item->status] ?? '#3b82f6',
                'extendedProps' => [
                    'title_raw' => $item->title,
                    'brief' => $item->brief_copywriting,
                    'platform' => $item->platform,
                    'status' => $item->status,
                    'assigned_to' => $item->assigned_to,
                    'assignee_name' => $item->assignee->name ?? 'Belum Ditentukan',
                    'scheduled_formatted' => $item->scheduled_at->format('d M Y, H:i'),
                ],
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'brief_copywriting' => 'nullable|string',
            'platform' => 'required|string|max:100',
            'status' => 'required|in:draft,production,review,scheduled,published',
            'scheduled_at' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $content = ContentCalendar::create([
            'title' => $request->title,
            'brief_copywriting' => $request->brief_copywriting,
            'platform' => $request->platform,
            'status' => $request->status,
            'scheduled_at' => $request->scheduled_at,
            'assigned_to' => $request->assigned_to,
            'created_by' => Auth::id(),
        ]);

        AuditLoggerService::log('Tambah Konten', "Jadwal konten baru [{$content->platform}] '{$content->title}'");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $content]);
        }

        return redirect()->route('content.index')->with('success', 'Jadwal konten berhasil ditambahkan.');
    }

    public function update(Request $request, ContentCalendar $contentCalendar)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'brief_copywriting' => 'nullable|string',
            'platform' => 'sometimes|required|string|max:100',
            'status' => 'sometimes|required|in:draft,production,review,scheduled,published',
            'scheduled_at' => 'sometimes|required|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $contentCalendar->update($request->only([
            'title',
            'brief_copywriting',
            'platform',
            'status',
            'scheduled_at',
            'assigned_to',
        ]));

        AuditLoggerService::log('Update Konten', "Pembaruan konten ID #{$contentCalendar->id} '{$contentCalendar->title}'");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $contentCalendar]);
        }

        return redirect()->route('content.index')->with('success', 'Jadwal konten berhasil diperbarui.');
    }

    public function destroy(ContentCalendar $contentCalendar)
    {
        AuditLoggerService::log('Hapus Konten', "Hapus jadwal konten '{$contentCalendar->title}'");
        $contentCalendar->delete();

        return redirect()->route('content.index')->with('success', 'Konten berhasil dihapus.');
    }
}
