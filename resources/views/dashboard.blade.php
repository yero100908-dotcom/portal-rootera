<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-dashboard text-indigo-400"></i> Dashboard Operasional & Modul Enterprise
            </h2>
            <p class="text-sm text-slate-400 mt-1">Selamat datang kembali, <span class="text-indigo-300 font-semibold">{{ $user->name }}</span> (Role: <span class="uppercase tracking-wide text-indigo-400 font-semibold">{{ $user->role }}</span>)</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-indigo-600/30 transition transform hover:-translate-y-0.5">
                <i class="ti ti-camera text-lg"></i> Scan Absensi Wajah
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Metric Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Card 1: Attendance Today -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-indigo-500/50 transition duration-300">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Presensi Hari Ini</p>
                        <h3 class="text-3xl font-extrabold text-white mt-1">{{ $todayCheckedIn }} <span class="text-xs font-normal text-slate-400">/ {{ $totalUsers }} user</span></h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30">
                        <i class="ti ti-user-check text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs text-slate-400">
                    <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-semibold">{{ $todayLate }} Terlambat</span>
                    <span>cut off: 08:00 WIB</span>
                </div>
            </div>

            <!-- Financial Cards for Owner & Admin -->
            @if($user->isOwner() || $user->isAdmin())
                <!-- Card 2: Total Pemasukan -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-emerald-500/50 transition duration-300">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Pemasukan</p>
                            <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">Rp {{ number_format($financials['total_inflow'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                            <i class="ti ti-arrow-up-right text-2xl"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-400">Cashflow masuk terverifikasi</p>
                </div>

                <!-- Card 3: Total Pengeluaran -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-rose-500/50 transition duration-300">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Pengeluaran</p>
                            <h3 class="text-2xl font-extrabold text-rose-400 mt-1">Rp {{ number_format($financials['total_outflow'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center border border-rose-500/30">
                            <i class="ti ti-arrow-down-left text-2xl"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-400">Operasional & Gaji Mentor</p>
                </div>

                <!-- Card 4: Saldo / Laba Bersih -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group hover:border-purple-500/50 transition duration-300">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl group-hover:bg-purple-500/20 transition"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Saldo / Laba Bersih</p>
                            <h3 class="text-2xl font-extrabold text-purple-300 mt-1">Rp {{ number_format($financials['net_profit'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center border border-purple-500/30">
                            <i class="ti ti-scale text-2xl"></i>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-slate-400">Saldo bersih saat ini</p>
                </div>
            @else
                <!-- Cards for Mentor / Karyawan / Siswa -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Status Kehadiran Anda</p>
                            <h3 class="text-xl font-bold text-indigo-300 mt-1">
                                @if($personalAttendance->first() && $personalAttendance->first()->waktu_presensi->isToday())
                                    <span class="text-emerald-400"><i class="ti ti-circle-check"></i> Sudah Presensi Hari Ini</span>
                                @else
                                    <span class="text-amber-400"><i class="ti ti-clock font-normal"></i> Belum Presensi</span>
                                @endif
                            </h3>
                        </div>
                    </div>
                    <a href="{{ route('attendance.create') }}" class="mt-4 inline-block text-xs font-semibold text-indigo-400 hover:underline">Buka kamera presensi &rarr;</a>
                </div>

                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Jadwal Konten Mendatang</p>
                            <h3 class="text-3xl font-extrabold text-indigo-300 mt-1">{{ $upcomingContents->count() }} <span class="text-xs font-normal text-slate-400">post</span></h3>
                        </div>
                    </div>
                    <a href="{{ route('content.index') }}" class="mt-4 inline-block text-xs font-semibold text-indigo-400 hover:underline">Lihat kalender &rarr;</a>
                </div>
            @endif

        </div>

        <!-- Section 2: Recent Activity & Quick Modules -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Content Schedule & Orders -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Content Planner Widget -->
                @if($user->isOwner() || $user->isAdmin() || $user->isMentor())
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="ti ti-calendar-event text-indigo-400"></i> Agendakan Konten Sosial Media
                                </h3>
                                <p class="text-xs text-slate-400">Jadwal publikasi konten mendatang di Instagram, TikTok, YouTube</p>
                            </div>
                            <a href="{{ route('content.index') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 rounded-lg transition border border-slate-700">
                                Buka Kalender Full &rarr;
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($upcomingContents as $content)
                                <div class="bg-slate-950/70 border border-slate-800 rounded-xl p-4 flex items-center justify-between hover:border-slate-700 transition">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-600/20 border border-indigo-500/30 text-indigo-300 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($content->platform, 0, 2) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-slate-200">{{ $content->title }}</h4>
                                            <p class="text-xs text-slate-400 flex items-center gap-2 mt-0.5">
                                                <span><i class="ti ti-clock"></i> {{ $content->scheduled_at->format('d M Y, H:i') }} WIB</span>
                                                <span>&bull;</span>
                                                <span>Assignee: {{ $content->assignee->name ?? 'Belum ada' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full uppercase border
                                        @if($content->status === 'published') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                        @elseif($content->status === 'scheduled') bg-purple-500/20 text-purple-300 border-purple-500/30
                                        @elseif($content->status === 'review') bg-blue-500/20 text-blue-300 border-blue-500/30
                                        @elseif($content->status === 'production') bg-amber-500/20 text-amber-300 border-amber-500/30
                                        @else bg-slate-700 text-slate-300 border-slate-600 @endif">
                                        {{ $content->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-500 text-sm">
                                    Belum ada jadwal konten mendatang.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- Recent Orders Table (Owner & Admin) -->
                @if($user->isOwner() || $user->isAdmin())
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="ti ti-shopping-cart text-indigo-400"></i> Transaksi Order Terkini
                                </h3>
                                <p class="text-xs text-slate-400">Rincian pesanan terbaru dari klien & status pembayaran</p>
                            </div>
                            <a href="{{ route('orders.index') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 rounded-lg transition border border-slate-700">
                                Kelola Order &rarr;
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-300">
                                <thead class="bg-slate-950/80 text-xs uppercase font-semibold text-slate-400 border-b border-slate-800">
                                    <tr>
                                        <th class="px-4 py-3">No Order</th>
                                        <th class="px-4 py-3">Klien</th>
                                        <th class="px-4 py-3">Nominal</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    @forelse($recentOrders as $ord)
                                        <tr class="hover:bg-slate-800/40">
                                            <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-300">{{ $ord->order_number }}</td>
                                            <td class="px-4 py-3 font-medium text-white">{{ $ord->client_name }}</td>
                                            <td class="px-4 py-3 font-semibold text-slate-200">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded-full border
                                                    @if($ord->payment_status === 'lunas') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                                    @elseif($ord->payment_status === 'dp') bg-amber-500/20 text-amber-300 border-amber-500/30
                                                    @elseif($ord->payment_status === 'pending') bg-blue-500/20 text-blue-300 border-blue-500/30
                                                    @else bg-rose-500/20 text-rose-300 border-rose-500/30 @endif">
                                                    {{ $ord->payment_status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-slate-500 text-xs">Belum ada order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Col: Attendance Snapshot Logs -->
            <div class="space-y-8">
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-clock-check text-indigo-400"></i> Presensi Terbaru
                    </h3>

                    <div class="space-y-4">
                        @php
                            $displayAttendance = ($user->isOwner() || $user->isAdmin()) ? $todayAttendance : $personalAttendance;
                        @endphp

                        @forelse($displayAttendance->take(5) as $att)
                            <div class="flex items-center gap-3 p-3 bg-slate-950/60 border border-slate-800/80 rounded-xl">
                                @if($att->foto_bukti)
                                    <img src="{{ asset($att->foto_bukti) }}" alt="Snapshot" class="w-12 h-12 rounded-lg object-cover border border-slate-700">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400">
                                        <i class="ti ti-user text-xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-bold text-white truncate">{{ $att->user->name ?? 'User' }}</h5>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        <span class="uppercase font-semibold text-indigo-300">{{ $att->tipe }}</span> &bull; {{ $att->waktu_presensi->format('H:i') }} WIB
                                    </p>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded border
                                    @if($att->status === 'tepat_waktu') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                    @else bg-rose-500/20 text-rose-300 border-rose-500/30 @endif">
                                    {{ str_replace('_', ' ', $att->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 text-center py-4">Belum ada aktivitas presensi.</p>
                        @endforelse
                    </div>

                    <a href="{{ route('attendance.index') }}" class="block w-full text-center mt-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-indigo-300 rounded-xl transition border border-slate-700">
                        Lihat Seluruh Rekap Absensi &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
