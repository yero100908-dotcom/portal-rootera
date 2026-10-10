<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'submission_file',
        'submission_url',
        'notes',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isLate(): bool
    {
        if (! $this->assignment) {
            return false;
        }

        $effectiveDeadline = $this->assignment->getEffectiveDeadlineForUser($this->user_id);

        return $effectiveDeadline ? $this->submitted_at->greaterThan($effectiveDeadline) : false;
    }

    public function isGraded(): bool
    {
        return ! is_null($this->score) || $this->status === 'graded';
    }

    public function isRevisionNeeded(): bool
    {
        return $this->status === 'revision_needed';
    }
}
