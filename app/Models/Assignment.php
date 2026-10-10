<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_attachment',
        'deadline',
        'created_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function overrides(): HasMany
    {
        return $this->hasMany(AssignmentUserOverride::class);
    }

    public function isOverdue(): bool
    {
        return now()->greaterThan($this->deadline);
    }

    public function getOverrideForUser($user): ?AssignmentUserOverride
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($this->relationLoaded('overrides')) {
            return $this->overrides->firstWhere('user_id', $userId);
        }

        return $this->overrides()->where('user_id', $userId)->first();
    }

    public function getEffectiveDeadlineForUser($user)
    {
        $override = $this->getOverrideForUser($user);

        if ($override && $override->is_unlocked && $override->custom_due_date) {
            return $override->custom_due_date;
        }

        return $this->deadline;
    }

    public function isOverdueForUser($user): bool
    {
        $effectiveDeadline = $this->getEffectiveDeadlineForUser($user);

        return $effectiveDeadline ? now()->greaterThan($effectiveDeadline) : false;
    }

    public function getSubmissionForUser($user): ?AssignmentSubmission
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($this->relationLoaded('submissions')) {
            return $this->submissions->firstWhere('user_id', $userId);
        }

        return $this->submissions()->where('user_id', $userId)->first();
    }
}
