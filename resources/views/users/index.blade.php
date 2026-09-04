<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen User & Template Wajah</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pengelolaan hak akses role pengguna dan registrasi descriptor pencocokan presensi</p>
            </div>
            <a href="{{ route('users.create') }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i class="ti ti-user-plus text-sm"></i> Tambah User Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search & Filter Card -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 sm:p-5 shadow-sm transition-colors duration-200">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="w-full sm:w-48">
                    <select name="role" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Semua Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ strtoupper($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                    <i class="ti ti-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-950 text-[11px] uppercase font-semibold text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Nama & Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">No Telepon</th>
                            <th class="px-4 py-3">Data Wajah (Descriptor)</th>
                            <th class="px-4 py-3">Status Akun</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $u->name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">{{ $u->email }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded border
                                        @if($u->role === 'owner') bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20
                                        @elseif($u->role === 'admin') bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-500/20
                                        @elseif($u->role === 'mentor') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                        @else bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20 @endif">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono">{{ $u->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($u->hasRegisteredFace())
                                        <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                            <i class="ti ti-circle-check"></i> Terdaftar
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 italic">Belum Ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($u->is_active)
                                        <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    <a href="{{ route('users.register-face', $u->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-300 text-[11px] font-semibold rounded border border-slate-200 dark:border-slate-700 transition">
                                        <i class="ti ti-scan"></i> Scan Wajah
                                    </a>
                                    <a href="{{ route('users.edit', $u->id) }}" class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[11px] font-semibold rounded border border-slate-200 dark:border-slate-700 transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                    Tidak ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
