<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="font-bold text-xl text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                    <i class="ti ti-edit text-indigo-600 dark:text-indigo-400"></i> Edit User {{ $user->name }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui data profil, role, atau ubah kata sandi</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg text-xs transition border border-slate-200 dark:border-slate-700">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm transition-colors duration-200">
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Email *</label>
                <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Ubah Password (Opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Role *</label>
                    <select name="role" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ strtoupper($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">No Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Status Akun *</label>
                <select name="is_active" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif (Blokir Akses)</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg text-xs border border-slate-200 dark:border-slate-700 transition">Batal</a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg text-xs shadow-sm transition">Perbarui User</button>
            </div>
        </form>
    </div>
</x-app-layout>
