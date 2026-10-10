<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentReportController extends Controller
{
    /**
     * Display the monthly assignment report for mentors and owners.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        if (! ($user->isOwner() || $user->isMentor() || $user->hasAnyRole(['owner', 'mentor']))) {
            abort(403, 'Akses Ditolak. Halaman Rekap Tugas Bulanan khusus untuk role Owner dan Mentor.');
        }

        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        // Query assignments created in the selected month & year
        $assignments = Assignment::with(['submissions.user'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAssignments = $assignments->count();

        // Target users to evaluate (exclude owner and mentor)
        $users = User::where('is_active', true)
            ->whereNotIn('role', ['owner', 'mentor'])
            ->orderBy('name')
            ->get();

        // Submissions for these assignments
        $assignmentIds = $assignments->pluck('id');
        $allSubmissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->get();

        $totalSubmissions = $allSubmissions->count();
        $totalLateSubmissions = $allSubmissions->filter(fn ($sub) => $sub->isLate())->count();

        // Team completion rate (% submission rate)
        $totalPossibleSubmissions = $users->count() * $totalAssignments;
        $submissionRate = $totalPossibleSubmissions > 0
            ? round(($totalSubmissions / $totalPossibleSubmissions) * 100, 1)
            : 0;

        // Build member recap collection
        $memberReports = $users->map(function ($u) use ($assignments, $totalAssignments) {
            $userSubmissions = $assignments->flatMap->submissions->where('user_id', $u->id);
            $submittedCount = $userSubmissions->count();
            $onTimeCount = $userSubmissions->filter(fn ($s) => ! $s->isLate())->count();
            $lateCount = $userSubmissions->filter(fn ($s) => $s->isLate())->count();
            $missingCount = max(0, $totalAssignments - $submittedCount);
            $complianceRate = $totalAssignments > 0
                ? round(($submittedCount / $totalAssignments) * 100, 1)
                : 0;

            // Detail assignments for modal / inspection
            $details = $assignments->map(function ($assignment) use ($u) {
                $sub = $assignment->submissions->firstWhere('user_id', $u->id);

                return [
                    'assignment_id' => $assignment->id,
                    'title' => $assignment->title,
                    'deadline' => $assignment->deadline ? $assignment->deadline->format('d M Y H:i') : '-',
                    'is_submitted' => ! is_null($sub),
                    'submitted_at' => $sub && $sub->submitted_at ? $sub->submitted_at->format('d M Y H:i') : null,
                    'is_late' => $sub ? $sub->isLate() : false,
                    'score' => $sub ? $sub->score : null,
                    'feedback' => $sub ? $sub->feedback : null,
                ];
            })->values();

            return [
                'user' => $u,
                'total_assigned' => $totalAssignments,
                'submitted_count' => $submittedCount,
                'ontime_count' => $onTimeCount,
                'late_count' => $lateCount,
                'missing_count' => $missingCount,
                'compliance_rate' => $complianceRate,
                'details' => $details,
            ];
        });

        $monthsList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('assignments.rekap-bulanan', compact(
            'month',
            'year',
            'assignments',
            'totalAssignments',
            'totalSubmissions',
            'totalLateSubmissions',
            'submissionRate',
            'memberReports',
            'monthsList'
        ));
    }

    /**
     * Export the monthly assignment report as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $user = auth()->user();
        if (! ($user->isOwner() || $user->isMentor() || $user->hasAnyRole(['owner', 'mentor']))) {
            abort(403, 'Akses Ditolak. Halaman Rekap Tugas Bulanan khusus untuk role Owner dan Mentor.');
        }

        $month = (int) $request->input('month', date('n'));
        $year = (int) $request->input('year', date('Y'));

        $monthsList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $monthName = $monthsList[$month] ?? $month;
        $fileName = "rekap-tugas-{$monthName}-{$year}.csv";

        $assignments = Assignment::with(['submissions.user'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $totalAssignments = $assignments->count();

        $users = User::where('is_active', true)
            ->whereNotIn('role', ['owner', 'mentor'])
            ->orderBy('name')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($users, $assignments, $totalAssignments) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Nama Karyawan',
                'Email',
                'Role / Divisi',
                'Total Tugas Ditugaskan',
                'Tugas Diselesaikan',
                'Tepat Waktu',
                'Terlambat',
                'Belum Dikumpulkan',
                'Persentase Kepatuhan (%)',
            ]);

            $no = 1;
            foreach ($users as $u) {
                $userSubmissions = $assignments->flatMap->submissions->where('user_id', $u->id);
                $submittedCount = $userSubmissions->count();
                $onTimeCount = $userSubmissions->filter(fn ($s) => ! $s->isLate())->count();
                $lateCount = $userSubmissions->filter(fn ($s) => $s->isLate())->count();
                $missingCount = max(0, $totalAssignments - $submittedCount);
                $complianceRate = $totalAssignments > 0
                    ? round(($submittedCount / $totalAssignments) * 100, 1)
                    : 0;

                fputcsv($handle, [
                    $no++,
                    $u->name,
                    $u->email,
                    strtoupper($u->role),
                    $totalAssignments,
                    $submittedCount,
                    $onTimeCount,
                    $lateCount,
                    $missingCount,
                    $complianceRate.'%',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
