<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials with Email, Username, or Role alias.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $rawInput = trim($this->input('email'));
        $cleanInput = strtolower($rawInput);
        $password = $this->input('password');

        // Alias mapping for quick login inputs like "owner", "admin", "mentor", "karyawan", "siswa"
        $aliasMap = [
            'owner' => 'owner@absensi.com',
            'admin' => 'admin@absensi.com',
            'mentor' => 'mentor@absensi.com',
            'karyawan' => 'karyawan@absensi.com',
            'siswa' => 'siswa@absensi.com',
        ];

        $targetEmail = $aliasMap[$cleanInput] ?? $rawInput;

        // 1. Try direct auth with targetEmail
        if (Auth::attempt(['email' => $targetEmail, 'password' => $password], $this->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // 2. Search user by role or name if direct email lookup fails
        $user = User::where('email', $targetEmail)
            ->orWhere('role', $cleanInput)
            ->orWhere('name', 'LIKE', "%{$rawInput}%")
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $this->boolean('remember'));
            RateLimiter::clear($this->throttleKey());
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Kredensial login tidak cocok. Gunakan email (owner@absensi.com, admin@absensi.com, dll) atau username role (owner, admin, mentor, karyawan).',
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
