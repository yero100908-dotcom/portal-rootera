<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">System Audit Trail & Audit Logs</h1>
                <p class="text-xs text-slate-400 mt-0.5">Pencatatan rekam jejak aktivitas pengubahan data (Owner Exclusive)</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search Filter -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('audit_logs.index') }}" class="flex gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi atau deskripsi log..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                    <i class="ti ti-search"></i> Cari Log
                </button>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-[11px] uppercase font-semibold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Waktu Log</th>
                            <th class="px-4 py-3">User Executable</th>
                            <th class="px-4 py-3">Aksi</th>
                            <th class="px-4 py-3">Deskripsi Audit</th>
                            <th class="px-4 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-4 py-3 text-xs text-slate-400 font-mono">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white">{{ $log->user->name ?? 'System / Anonymous' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ strtoupper($log->user->role ?? '-') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded border bg-indigo-500/10 text-indigo-400 border-indigo-500/20">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-200">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-slate-400 font-mono">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-500">Belum ada catatan audit log.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
