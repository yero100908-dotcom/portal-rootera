<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-file-text text-indigo-400"></i> Rekap Absensi & Log Presensi Wajah
            </h2>
            <p class="text-sm text-slate-400 mt-1">Laporan histori kehadiran real-time lengkap dengan bukti snapshot & verifikasi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.export', request()->all()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-md transition flex items-center gap-2">
                <i class="ti ti-download text-lg"></i> Ekspor CSV / Excel
            </a>
            <a href="{{ route('attendance.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-sm shadow-md transition flex items-center gap-2">
                <i class="ti ti-camera text-lg"></i> Presensi Kamera
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Filter Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-950 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-950 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Role User</label>
                    <select name="role" class="w-full bg-slate-950 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ strtoupper($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Kehadiran</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Status --</option>
                        <option value="tepat_waktu" {{ request('status') === 'tepat_waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl transition shadow">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    <a href="{{ route('attendance.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-xl border border-slate-700">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Attendance Logs Table Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">User & Role</th>
                            <th class="px-4 py-3">Bukti Snapshot</th>
                            <th class="px-4 py-3">Waktu Presensi</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Verifikasi</th>
                            @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                                <th class="px-4 py-3 text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($attendances as $row)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white">{{ $row->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5">{{ strtoupper($row->user->role ?? '-') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($row->foto_bukti)
                                        <a href="{{ asset($row->foto_bukti) }}" target="_blank" class="inline-block">
                                            <img src="{{ asset($row->foto_bukti) }}" alt="Snapshot" class="w-12 h-12 rounded-lg object-cover border border-slate-700 hover:scale-110 transition shadow">
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-500 italic">No image</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-200">{{ $row->waktu_presensi->format('d M Y') }}</div>
                                    <div class="text-xs text-slate-400 font-mono"><i class="ti ti-clock"></i> {{ $row->waktu_presensi->format('H:i:s') }} WIB</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg border
                                        @if($row->tipe === 'masuk') bg-teal-500/20 text-teal-300 border-teal-500/30
                                        @else bg-purple-500/20 text-purple-300 border-purple-500/30 @endif">
                                        {{ $row->tipe }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg border
                                        @if($row->status === 'tepat_waktu') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                        @elseif($row->status === 'terlambat') bg-rose-500/20 text-rose-300 border-rose-500/30
                                        @else bg-amber-500/20 text-amber-300 border-amber-500/30 @endif">
                                        {{ str_replace('_', ' ', $row->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($row->is_verified)
                                        <span class="text-emerald-400 font-semibold flex items-center gap-1">
                                            <i class="ti ti-circle-check font-bold"></i> Verified
                                        </span>
                                    @else
                                        <span class="text-amber-400 font-semibold flex items-center gap-1">
                                            <i class="ti ti-clock"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                                    <td class="px-4 py-3 text-right">
                                        @if(!$row->is_verified)
                                            <form method="POST" action="{{ route('attendance.verify', $row->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg shadow">
                                                    Verifikasi Manual
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-500">Selesai</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-500">
                                    Tidak ada data presensi yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
