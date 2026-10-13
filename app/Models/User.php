<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'photo',
        'face_descriptor',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function cashflows(): HasMany
    {
        return $this->hasMany(Cashflow::class, 'created_by');
    }

    public function contentCalendars(): HasMany
    {
        return $this->hasMany(ContentCalendar::class, 'created_by');
    }

    public function assignedContents(): HasMany
    {
        return $this->hasMany(ContentCalendar::class, 'assigned_to');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'created_by');
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner' || $this->hasRole('owner');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isMentor(): bool
    {
        return $this->role === 'mentor' || $this->hasRole('mentor');
    }

    public function isIT(): bool
    {
        return $this->role === 'it' || $this->hasRole('it');
    }

    public function isSosmed(): bool
    {
        return $this->role === 'sosmed' || $this->hasRole('sosmed');
    }

    public function isCS(): bool
    {
        return $this->role === 'cs' || $this->hasRole('cs');
    }

    public function isKaryawanOrSiswa(): bool
    {
        return in_array($this->role, ['karyawan', 'siswa']) || $this->hasAnyRole(['karyawan', 'siswa']);
    }

    public function canManageAssignments(): bool
    {
        return $this->isOwner() || $this->isMentor() || $this->hasAnyRole(['owner', 'mentor']);
    }

    public function canManageOfficeLocation(): bool
    {
        return $this->isOwner() || $this->isMentor() || $this->hasAnyRole(['owner', 'mentor']);
    }

    public function canManageOrders(): bool
    {
        return $this->isOwner() || $this->isAdmin() || $this->isCS() || $this->isMentor();
    }

    public function canManageContent(): bool
    {
        return $this->isOwner() || $this->isAdmin() || $this->isMentor() || $this->isSosmed();
    }

    public function canManageFinance(): bool
    {
        return $this->isOwner() || $this->isAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->isOwner() || $this->isAdmin() || $this->isIT();
    }

    public function canViewAuditLogs(): bool
    {
        return $this->isOwner() || $this->isIT();
    }

    public function hasRegisteredFace(): bool
    {
        return ! empty($this->face_descriptor);
    }
}
