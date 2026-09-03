<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Owner/Admin.']);
        }

        // Owner always has full access to everything
        if ($user->role === 'owner' || $user->hasRole('owner')) {
            return $next($request);
        }

        // Check if user has any of the requested roles
        $hasPermission = false;
        foreach ($roles as $role) {
            if ($user->role === $role || $user->hasRole($role)) {
                $hasPermission = true;
                break;
            }
        }

        if (! $hasPermission) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki wewenang untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
