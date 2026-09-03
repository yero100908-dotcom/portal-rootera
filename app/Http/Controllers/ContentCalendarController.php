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

        $statusColors = [
            'draft' => '#64748b',      // Slate
            'production' => '#f59e0b', // Amber
            'review' => '#3b82f6',     // Blue
            'scheduled' => '#a855f7',  // Purple
            'published' => '#10b981',  // Emerald
        ];

        $platformColors = [
            'Instagram' => '#c13584',
            'TikTok' => '#00f2fe',
            'YouTube' => '#ef4444',
            'Facebook' => '#1877f2',
            'LinkedIn' => '#0a66c2',
            'Twitter/X' => '#38bdf8',
        ];

        $platformIcons = [
            'Instagram' => 'ti-brand-instagram',
            'TikTok' => 'ti-brand-tiktok',
            'YouTube' => 'ti-brand-youtube',
            'Facebook' => 'ti-brand-facebook',
            'LinkedIn' => 'ti-brand-linkedin',
            'Twitter/X' => 'ti-brand-x',
        ];

        $events = $items->map(function ($item) use ($statusColors, $platformColors, $platformIcons) {
            $statusColor = $statusColors[$item->status] ?? '#3b82f6';
            $platformColor = $platformColors[$item->platform] ?? '#6366f1';
            $icon = $platformIcons[$item->platform] ?? 'ti-brand-line';

            return [
                'id' => $item->id,
                'title' => $item->title,
                'start' => $item->scheduled_at->toIso8601String(),
                'backgroundColor' => $statusColor,
                'borderColor' => $statusColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'title_raw' => $item->title,
                    'brief' => $item->brief_copywriting,
                    'platform' => $item->platform,
                    'platform_color' => $platformColor,
                    'platform_icon' => $icon,
                    'status' => $item->status,
                    'status_color' => $statusColor,
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
