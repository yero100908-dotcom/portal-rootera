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

    public function isOverdue(): bool
    {
        return now()->greaterThan($this->deadline);
    }

    public function getSubmissionForUser($user): ?AssignmentSubmission
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->submissions()->where('user_id', $userId)->first();
    }
}
