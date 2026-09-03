<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center space-y-1.5">
            <h1 class="text-lg font-bold text-white tracking-tight">Selamat Datang Kembali</h1>
            <p class="text-xs text-slate-400">Masukkan akun Anda untuk melanjutkan ke sistem</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-lg text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email or Username Input -->
            <div>
                <label for="email" class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    Email / Username
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="ti ti-user text-base"></i>
                    </div>
                    <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus 
                           placeholder="nama@email.com atau username"
                           class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl pl-10 pr-3.5 py-2.5 text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition placeholder:text-slate-600 shadow-inner">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-400 bg-rose-500/10 border border-rose-500/20 p-2.5 rounded-lg" />
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-[11px] text-indigo-400 hover:text-indigo-300 font-medium transition" href="{{ route('password.request') }}">
                            Lupa Password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i class="ti ti-lock text-base"></i>
                    </div>
                    <input id="password" type="password" name="password" required 
                           placeholder="••••••••"
                           class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl pl-10 pr-3.5 py-2.5 text-xs focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition placeholder:text-slate-600 shadow-inner">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-400 bg-rose-500/10 border border-rose-500/20 p-2.5 rounded-lg" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500/50 focus:ring-offset-slate-900">
                    <span class="ms-2 text-xs text-slate-400">Ingat Saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/20 transition duration-150 flex items-center justify-center gap-2 group mt-2">
                <span>Masuk Ke Sistem</span>
                <i class="ti ti-arrow-right text-sm group-hover:translate-x-0.5 transition-transform"></i>
            </button>
        </form>
    </div>
</x-guest-layout>
