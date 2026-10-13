<x-app-layout>
    <x-slot name="header">
        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight truncate flex items-center gap-2">
            <i class="ti ti-calendar-user text-indigo-600 dark:text-indigo-400 shrink-0"></i>
            <span class="truncate">Rekap Absensi & Log Presensi</span>
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Page Hero Banner -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Rekap Absensi & Log Presensi</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Histori kehadiran real-time lengkap dengan bukti foto, koordinat GPS & verifikasi geofencing</p>
            </div>
            <div class="flex items-center flex-wrap gap-2 shrink-0">
                <a href="{{ route('attendance.export', request()->all()) }}" class="flex-1 sm:flex-initial px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                    <i class="ti ti-download text-sm"></i> Ekspor CSV
                </a>
                <a href="{{ route('attendance.create') }}" class="flex-1 sm:flex-initial px-3.5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                    <i class="ti ti-camera text-sm"></i> Scan Kamera
                </a>
            </div>
        </div>
        <!-- Filter Card -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 sm:p-5 shadow-sm transition-colors duration-200">
            <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Role User</label>
                    <select name="role" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Semua Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ strtoupper($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Status Kehadiran</label>
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Semua Status --</option>
                        <option value="tepat_waktu" {{ request('status') === 'tepat_waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    <a href="{{ route('attendance.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Attendance Logs Table -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-950 text-[11px] uppercase font-semibold text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">User & Role</th>
                            <th class="px-4 py-3">Bukti Snapshot</th>
                            <th class="px-4 py-3">Waktu Presensi</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Lokasi GPS (Geofence)</th>
                            <th class="px-4 py-3">Verifikasi</th>
                            @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                                <th class="px-4 py-3 text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                        @forelse($attendances as $row)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $row->user->name ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">{{ strtoupper($row->user->role ?? '-') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $photoUrl = null;
                                        if ($row->foto_bukti) {
                                            $photoUrl = str_starts_with($row->foto_bukti, 'http')
                                                ? $row->foto_bukti
                                                : (str_starts_with($row->foto_bukti, 'storage/') 
                                                    ? asset($row->foto_bukti) 
                                                    : asset('storage/' . $row->foto_bukti));
                                        }
                                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($row->user->name ?? 'User') . '&background=1e293b&color=cbd5e1';
                                    @endphp
                                    <a href="{{ $photoUrl ?? $avatarUrl }}" target="_blank" class="inline-block">
                                        <img src="{{ $photoUrl ?? $avatarUrl }}" alt="Snapshot" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700 hover:scale-105 transition shadow-sm" onError="this.onerror=null; this.src='{{ $avatarUrl }}';">
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-800 dark:text-slate-200">{{ $row->waktu_presensi->format('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $row->waktu_presensi->format('H:i:s') }} WIB</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                        @if($row->tipe === 'masuk') bg-teal-500/10 text-teal-700 dark:text-teal-400 border-teal-500/20
                                        @else bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20 @endif">
                                        {{ $row->tipe }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                        @if($row->status === 'tepat_waktu') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                        @elseif($row->status === 'terlambat') bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20
                                        @else bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20 @endif">
                                        {{ str_replace('_', ' ', $row->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($row->distance !== null)
                                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                            <i class="ti ti-map-pin"></i> {{ $row->distance }}m
                                        </span>
                                        <span class="text-[10px] text-slate-500 font-mono block">{{ number_format($row->latitude, 4) }}, {{ number_format($row->longitude, 4) }}</span>
                                    @else
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 italic">Manual / Legacy</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($row->is_verified)
                                        <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                            <i class="ti ti-check"></i> Verified
                                        </span>
                                    @else
                                        <span class="text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1">
                                            <i class="ti ti-clock"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                                    <td class="px-4 py-3 text-right">
                                        @if(!$row->is_verified)
                                            <form method="POST" action="{{ route('attendance.verify', $row->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-semibold rounded transition shadow-sm">
                                                    Verifikasi
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-slate-400 dark:text-slate-500">-</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                    Tidak ada data presensi yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
