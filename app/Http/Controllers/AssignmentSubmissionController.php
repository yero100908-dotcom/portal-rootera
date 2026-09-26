<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    /**
     * Store or update a submission for an assignment.
     */
    public function store(Request $request, Assignment $assignment)
    {
        if (Auth::user()->canManageAssignments()) {
            return back()->with('error', 'Owner, Mentor, dan Admin adalah pembuat tugas dan tidak mengumpulkan jawaban.');
        }

        if (! $assignment->is_active) {
            return back()->with('error', 'Tugas ini sudah tidak aktif.');
        }

        $request->validate([
            'submission_file' => 'nullable|file|max:20480',
            'submission_url' => 'nullable|url|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (! $request->hasFile('submission_file') && ! $request->filled('submission_url') && ! $request->filled('notes')) {
            return back()->with('error', 'Harap lampirkan sekurang-kurangnya file tugas, link repositori/dokumen, atau catatan pengerjaan.')->withInput();
        }

        $existingSubmission = $assignment->getSubmissionForUser(Auth::user());

        // Save file if uploaded
        $filePath = $existingSubmission ? $existingSubmission->submission_file : null;
        if ($request->hasFile('submission_file')) {
            if ($existingSubmission && $existingSubmission->submission_file) {
                Storage::disk('public')->delete($existingSubmission->submission_file);
            }
            $filePath = $request->file('submission_file')->store('assignments/submissions', 'public');
        }

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
            ],
            [
                'submission_file' => $filePath,
                'submission_url' => $request->input('submission_url'),
                'notes' => $request->input('notes'),
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('assignments.show', $assignment->id)
            ->with('success', 'Pengumpulan tugas Anda berhasil disimpan.');
    }

    /**
     * Input grade (score) and feedback for a submission (Manager only).
     */
    public function grade(Request $request, AssignmentSubmission $submission)
    {
        if (! Auth::user()->canManageAssignments()) {
            abort(403, 'Anda tidak memiliki akses untuk memberikan nilai.');
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
        ]);

        return back()->with('success', "Nilai untuk {$submission->user->name} berhasil diperbarui.");
    }
}
