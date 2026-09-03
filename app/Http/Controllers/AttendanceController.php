<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Services\AuditLoggerService;
use App\Services\FaceRecognitionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    protected FaceRecognitionService $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->faceService = $faceService;
    }

    /**
     * Dashboard Rekap Absensi
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $query = Attendance::with('user', 'verifier');

        // Non-admin/owner only sees their own attendance
        if ($currentUser->isKaryawanOrSiswa()) {
            $query->where('user_id', $currentUser->id);
        }

        // Filters
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_presensi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('waktu_presensi', '<=', $request->end_date);
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $attendances = $query->latest('waktu_presensi')->paginate(15)->withQueryString();
        $roles = ['karyawan', 'siswa', 'mentor', 'admin', 'owner'];

        return view('attendance.index', compact('attendances', 'roles'));
    }

    /**
     * Tampilan Kamera Presensi Wajah Real-time
     */
    public function create()
    {
        $users = User::where('is_active', true)->select('id', 'name', 'email', 'role', 'face_descriptor')->get();

        return view('attendance.create', compact('users'));
    }

    /**
     * Process Real-Time Face Attendance Submission
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:masuk,pulang',
            'image' => 'required|string', // Base64 image snapshot
            'face_descriptor' => 'nullable|string', // Optional array descriptor from frontend
            'user_id' => 'nullable|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $targetUser = $currentUser;

        // If user descriptor provided or admin manually selecting user
        if ($request->filled('user_id') && ($currentUser->isOwner() || $currentUser->isAdmin())) {
            $targetUser = User::findOrFail($request->user_id);
        } elseif ($request->filled('face_descriptor')) {
            $descriptor = json_decode($request->face_descriptor, true);
            if (is_array($descriptor)) {
                $matchedUser = $this->faceService->matchFace($descriptor);
                if ($matchedUser) {
                    $targetUser = $matchedUser;
                }
            }
        }

        // Process Base64 Snapshot Image
        $imageData = $request->image;
        if (str_contains($imageData, ',')) {
            $imageData = explode(',', $imageData)[1];
        }
        $decodedImage = base64_decode($imageData);
        $fileName = 'attendance/' . Str::slug($targetUser->name) . '_' . time() . '_' . Str::random(6) . '.jpg';
        Storage::disk('public')->put($fileName, $decodedImage);

        // Check time status (Standard check-in time: 08:00 AM)
        $now = now();
        $status = 'tepat_waktu';
        if ($request->tipe === 'masuk') {
            $cutoff = Carbon::createFromTimeString('08:00:00');
            if ($now->greaterThan($cutoff)) {
                $status = 'terlambat';
            }
        }

        $attendance = Attendance::create([
            'user_id' => $targetUser->id,
            'waktu_presensi' => $now,
            'tipe' => $request->tipe,
            'status' => $status,
            'catatan' => $request->catatan ?? null,
            'foto_bukti' => 'storage/' . $fileName,
            'is_verified' => true,
        ]);

        AuditLoggerService::log(
            'Presensi ' . ucfirst($request->tipe),
            "Presensi {$request->tipe} ({$status}) berhasil untuk {$targetUser->name}",
            $targetUser->id
        );

        return response()->json([
            'success' => true,
            'message' => "Presensi {$request->tipe} berhasil dicatat! Status: " . ($status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat'),
            'user' => [
                'name' => $targetUser->name,
                'role' => strtoupper($targetUser->role),
            ],
            'attendance' => $attendance,
        ]);
    }

    /**
     * Verifikasi Manual oleh Admin / Owner
     */
    public function verify(Attendance $attendance)
    {
        $currentUser = Auth::user();
        if (! $currentUser->isOwner() && ! $currentUser->isAdmin()) {
            abort(403);
        }

        $attendance->update([
            'is_verified' => true,
            'verified_by' => $currentUser->id,
        ]);

        AuditLoggerService::log('Verifikasi Absensi', "Verifikasi absensi ID #{$attendance->id} untuk user {$attendance->user->name}");

        return redirect()->back()->with('success', 'Presensi berhasil diverifikasi secara manual.');
    }

    /**
     * Export Rekap Absensi (CSV)
     */
    public function export(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->filled('start_date')) {
            $query->whereDate('waktu_presensi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('waktu_presensi', '<=', $request->end_date);
        }

        $attendances = $query->get();

        $filename = "rekap_presensi_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama User', 'Role', 'Waktu Presensi', 'Tipe', 'Status', 'Verifikasi']);

            foreach ($attendances as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->user->name ?? 'N/A',
                    strtoupper($row->user->role ?? '-'),
                    $row->waktu_presensi->format('Y-m-d H:i:s'),
                    strtoupper($row->tipe),
                    strtoupper($row->status),
                    $row->is_verified ? 'Ya' : 'Belum',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
