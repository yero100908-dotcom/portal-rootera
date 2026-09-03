<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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

    public function isKaryawanOrSiswa(): bool
    {
        return in_array($this->role, ['karyawan', 'siswa']) || $this->hasAnyRole(['karyawan', 'siswa']);
    }
}
