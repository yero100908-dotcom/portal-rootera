<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentUserOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'custom_due_date',
        'is_unlocked',
        'unlocked_by',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'custom_due_date' => 'datetime',
            'is_unlocked' => 'boolean',
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

    public function unlockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    public function isValid(): bool
    {
        return $this->is_unlocked && now()->lessThanOrEqualTo($this->custom_due_date);
    }
}
