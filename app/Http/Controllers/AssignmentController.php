<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isManager = $user->canManageAssignments();
        $query = Assignment::with(['creator', 'submissions']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true)->where('deadline', '>=', now());
            } elseif ($request->input('status') === 'overdue') {
                $query->where('deadline', '<', now());
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $submissionFilter = $request->input('tab', 'pending');

        if (! $isManager) {
            // For general employees/students, only show active assignments
            $query->where('is_active', true);

            if ($submissionFilter === 'pending') {
                // Hide assignments already submitted by this user (task disappears immediately!)
                $query->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($submissionFilter === 'submitted') {
                // Show assignments already submitted by this user
                $query->whereHas('submissions', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        $assignments = $query->latest()->paginate(10)->withQueryString();

        // Calculate stats for header summary
        $totalAssignments = Assignment::count();
        $activeAssignments = Assignment::where('is_active', true)->where('deadline', '>=', now())->count();

        // Stats for current user & manager overview
        $userSubmissions = $user->assignmentSubmissions->keyBy('assignment_id');
        $mySubmittedCount = $userSubmissions->count();
        $myGradedCount = $userSubmissions->whereNotNull('score')->count();
        $myPendingCount = Assignment::where('is_active', true)
            ->whereDoesntHave('submissions', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

        $totalSubmissionsCount = AssignmentSubmission::count();
        $totalGradedCount = AssignmentSubmission::whereNotNull('score')->count();

        return view('assignments.index', compact(
            'assignments',
            'isManager',
            'totalAssignments',
            'activeAssignments',
            'userSubmissions',
            'mySubmittedCount',
            'myGradedCount',
            'myPendingCount',
            'totalSubmissionsCount',
            'totalGradedCount',
            'submissionFilter'
        ));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        if (! Auth::user()->canManageAssignments()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat tugas.');
        }

        return view('assignments.create');
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request)
    {
        if (! Auth::user()->canManageAssignments()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat tugas.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'file_attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,zip,rar,png,jpg,jpeg,txt,xlsx,pptx',
            'is_active' => 'nullable|boolean',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('file_attachment')) {
            $attachmentPath = $request->file('file_attachment')->store('assignments/attachments', 'public');
        }

        Assignment::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'deadline' => $validated['deadline'],
            'file_attachment' => $attachmentPath,
            'created_by' => Auth::id(),
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        return redirect()->route('assignments.index')->with('success', 'Tugas baru berhasil dibuat.');
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment)
    {
        $user = Auth::user();
        $isManager = $user->canManageAssignments();

        $assignment->load(['creator', 'submissions.user']);

        // Non-manager users shouldn't access inactive assignments
        if (! $isManager && ! $assignment->is_active) {
            abort(404, 'Tugas ini sudah tidak aktif.');
        }

        $mySubmission = $assignment->getSubmissionForUser($user);

        // For manager view: list all non-owner users to track who submitted & who hasn't
        $allUsers = collect();
        $submittedUserIds = collect();

        if ($isManager) {
            // Get all active users who are NOT managers (assignees: karyawan, sosmed, cs, it, etc.)
            $allUsers = User::where('is_active', true)
                ->orderBy('name')
                ->get()
                ->reject(fn ($u) => $u->canManageAssignments())
                ->values();

            $submittedUserIds = $assignment->submissions->pluck('user_id');
        }

        return view('assignments.show', compact(
            'assignment',
            'isManager',
            'mySubmission',
            'allUsers',
            'submittedUserIds'
        ));
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(Assignment $assignment)
    {
        if (! Auth::user()->canManageAssignments()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tugas.');
        }

        return view('assignments.edit', compact('assignment'));
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        if (! Auth::user()->canManageAssignments()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit tugas.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'file_attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,zip,rar,png,jpg,jpeg,txt,xlsx,pptx',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('file_attachment')) {
            // Delete old attachment if exists
            if ($assignment->file_attachment) {
                Storage::disk('public')->delete($assignment->file_attachment);
            }
            $validated['file_attachment'] = $request->file('file_attachment')->store('assignments/attachments', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $assignment->update($validated);

        return redirect()->route('assignments.show', $assignment->id)->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(Assignment $assignment)
    {
        if (! Auth::user()->canManageAssignments()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus tugas.');
        }

        if ($assignment->file_attachment) {
            Storage::disk('public')->delete($assignment->file_attachment);
        }

        // Delete submission files
        foreach ($assignment->submissions as $submission) {
            if ($submission->submission_file) {
                Storage::disk('public')->delete($submission->submission_file);
            }
        }

        $assignment->delete();

        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
