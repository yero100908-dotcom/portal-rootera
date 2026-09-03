<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-shield-lock text-purple-400"></i> System Audit Trail & Audit Logs (Owner Exclusive)
            </h2>
            <p class="text-sm text-slate-400 mt-1">Pencatatan rekam jejak aktivitas penting pengubahan data, presensi, dan keuangan sistem</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search Filter -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <form method="GET" action="{{ route('audit_logs.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi atau deskripsi log..." class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-semibold text-sm rounded-xl transition shadow">
                    <i class="ti ti-search"></i> Cari Log
                </button>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Waktu Log</th>
                            <th class="px-4 py-3">User Executable</th>
                            <th class="px-4 py-3">Aksi</th>
                            <th class="px-4 py-3">Deskripsi Audit</th>
                            <th class="px-4 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3 text-xs text-slate-400 font-mono">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white">{{ $log->user->name ?? 'System / Anonymous' }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ strtoupper($log->user->role ?? '-') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-200">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-xs text-slate-400 font-mono">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-500">Belum ada catatan audit log.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
