<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Dashboard Operasional</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Overview real-time presensi, finansial, order, dan jadwal media sosial</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="dashboardApp()">
        
        <!-- Header Quick Action Controls -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/80 rounded-xl p-3.5 shadow-sm transition-colors duration-200">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Live System Status: Normal</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($user->canManageFinance())
                    <button @click="showTransactionModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                        <i class="ti ti-plus text-indigo-600 dark:text-indigo-400"></i> Catat Transaksi
                    </button>
                @endif

                @if($user->canManageOrders())
                    <button @click="showOrderModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                        <i class="ti ti-shopping-cart-plus text-emerald-600 dark:text-emerald-400"></i> Buat Order Baru
                    </button>
                @endif

                @if($user->canManageContent())
                    <button @click="showContentModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                        <i class="ti ti-calendar-plus text-purple-600 dark:text-purple-400"></i> Jadwal Konten
                    </button>
                @endif

                <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <i class="ti ti-camera"></i> Scan Kamera
                </a>
            </div>
        </div>

        <!-- 4-Column Responsive Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Metric 1: Presensi Hari Ini -->
            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors duration-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Presensi Hari Ini</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                        <i class="ti ti-user-check text-lg"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $todayCheckedIn }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">/ {{ $totalUsers }} User Aktif</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs">
                    <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $todayLate }} Terlambat</span>
                    <a href="{{ route('attendance.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Detail &rarr;</a>
                </div>
            </div>

            @if($user->canManageFinance())
                <!-- Metric 2: Total Pemasukan -->
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Pemasukan</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                            <i class="ti ti-arrow-up-right text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Rp {{ number_format($financials['total_inflow'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Cashflow & Order Lunas</span>
                        <a href="{{ route('finance.index') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">Arus Kas &rarr;</a>
                    </div>
                </div>

                <!-- Metric 3: Total Pengeluaran -->
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Pengeluaran</span>
                        <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20">
                            <i class="ti ti-arrow-down-left text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Rp {{ number_format($financials['total_outflow'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Operasional & Gaji</span>
                        <a href="{{ route('finance.index') }}" class="text-rose-600 dark:text-rose-400 hover:underline font-medium">Rincian &rarr;</a>
                    </div>
                </div>

                <!-- Metric 4: Saldo Bersih -->
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Saldo / Net Profit</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20">
                            <i class="ti ti-scale text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-300 tracking-tight">
                        Rp {{ number_format($financials['net_profit'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Selisih Net Real-time</span>
                        @if($user->isOwner())
                            <a href="{{ route('finance.profit_loss') }}" class="text-purple-600 dark:text-purple-400 hover:underline font-medium">Laba Rugi &rarr;</a>
                        @else
                            <span class="text-slate-400 dark:text-slate-500">Owner Verified</span>
                        @endif
                    </div>
                </div>
            @else
                <!-- Non-financial cards for Karyawan / Siswa -->
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Hari Ini</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                            <i class="ti ti-user-check text-lg"></i>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-white">
                        @if($personalAttendance->first() && $personalAttendance->first()->waktu_presensi->isToday())
                            <span class="text-emerald-600 dark:text-emerald-400">Sudah Presensi ({{ $personalAttendance->first()->waktu_presensi->format('H:i') }})</span>
                        @else
                            <span class="text-amber-600 dark:text-amber-400">Belum Presensi</span>
                        @endif
                    </div>
                    <a href="{{ route('attendance.create') }}" class="mt-3 block text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Absen Sekarang &rarr;</a>
                </div>

                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jadwal Konten</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20">
                            <i class="ti ti-calendar-event text-lg"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $upcomingContents->count() }} Post
                    </div>
                    <a href="{{ route('content.index') }}" class="mt-3 block text-xs text-purple-600 dark:text-purple-400 hover:underline font-medium">Lihat Kalender &rarr;</a>
                </div>
            @endif

        </div>

        <!-- 2-Column Responsive Section: Content Schedule & Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Content Planner & Recent Orders -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Content Planner Widget -->
                @if($user->canManageContent())
                    <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm transition-colors duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <i class="ti ti-calendar-event text-purple-600 dark:text-purple-400"></i> Agenda Konten Mendatang (Aktif)
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar konten yang belum berstatus Published</p>
                            </div>
                            <a href="{{ route('content.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                Buka Kalender &rarr;
                            </a>
                        </div>

                        <div class="space-y-2.5">
                            @forelse($upcomingContents as $content)
                                <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800/80 rounded-lg p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-slate-300 dark:hover:border-slate-700 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($content->platform, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $content->title }}</h4>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                                <span><i class="ti ti-clock"></i> {{ $content->scheduled_at->format('d M Y, H:i') }}</span>
                                                <span>&bull;</span>
                                                <span>PIC: {{ $content->assignee->name ?? 'Belum ada' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="self-start sm:self-auto px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                        @if($content->status === 'scheduled') bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20
                                        @elseif($content->status === 'review') bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20
                                        @elseif($content->status === 'production') bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                        @else bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700 @endif">
                                        {{ $content->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 dark:text-slate-500 text-xs">
                                    Belum ada agenda konten
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- Recent Orders Table -->
                @if($user->canManageOrders())
                    <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm transition-colors duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <i class="ti ti-shopping-cart text-emerald-600 dark:text-emerald-400"></i> Transaksi Order Terbaru
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Rincian order masuk dan status pembayaran</p>
                            </div>
                            <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                Kelola Order &rarr;
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                                <thead class="bg-slate-100 dark:bg-slate-950 text-[11px] uppercase font-semibold text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th class="px-3 py-2.5">No Order</th>
                                        <th class="px-3 py-2.5">Klien</th>
                                        <th class="px-3 py-2.5">Nominal</th>
                                        <th class="px-3 py-2.5">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                                    @forelse($recentOrders as $ord)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                            <td class="px-3 py-2.5 font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $ord->order_number }}</td>
                                            <td class="px-3 py-2.5 font-medium text-slate-900 dark:text-white">{{ $ord->client_name }}</td>
                                            <td class="px-3 py-2.5 font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</td>
                                            <td class="px-3 py-2.5">
                                                <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                                    @if($ord->payment_status === 'lunas') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                                    @elseif($ord->payment_status === 'dp') bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                                    @elseif($ord->payment_status === 'pending') bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20
                                                    @else bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20 @endif">
                                                    {{ $ord->payment_status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-slate-400 dark:text-slate-500 text-xs">Belum ada transaksi tercatat</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Col: Attendance Snapshots -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm transition-colors duration-200">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-clock-check text-indigo-600 dark:text-indigo-400"></i> Presensi Real-Time Hari Ini
                    </h3>

                    <div class="space-y-3">
                        @php
                            $displayAttendance = ($user->isOwner() || $user->isAdmin()) ? $todayAttendance : $personalAttendance;
                        @endphp

                        @forelse($displayAttendance->take(5) as $att)
                            @php
                                $photoUrl = null;
                                if ($att->foto_bukti) {
                                    $photoUrl = str_starts_with($att->foto_bukti, 'http')
                                        ? $att->foto_bukti
                                        : (str_starts_with($att->foto_bukti, 'storage/') 
                                            ? asset($att->foto_bukti) 
                                            : asset('storage/' . $att->foto_bukti));
                                }
                                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($att->user->name ?? 'User') . '&background=1e293b&color=cbd5e1';
                            @endphp
                            <div class="flex items-center gap-3 p-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800/80 rounded-lg">
                                <img src="{{ $photoUrl ?? $avatarUrl }}" alt="Snapshot" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shadow-sm" onError="this.onerror=null; this.src='{{ $avatarUrl }}';">
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $att->user->name ?? 'User' }}</h5>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        <span class="uppercase font-semibold text-indigo-600 dark:text-indigo-400">{{ $att->tipe }}</span> &bull; {{ $att->waktu_presensi->format('H:i') }} WIB
                                    </p>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                    @if($att->status === 'tepat_waktu') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                    @else bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20 @endif">
                                    {{ str_replace('_', ' ', $att->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 dark:text-slate-500 text-center py-4">Belum ada log presensi.</p>
                        @endforelse
                    </div>

                    <a href="{{ route('attendance.index') }}" class="block w-full text-center mt-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition border border-slate-200 dark:border-slate-700">
                        Lihat Seluruh Log Absensi &rarr;
                    </a>
                </div>
            </div>

        </div>

        <!-- ==================== MODAL FORM CRUD INTERACTIVITY ==================== -->

        <!-- 1. Modal Catat Transaksi Kas (Inflow / Outflow) -->
        <div x-show="showTransactionModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="showTransactionModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-xl p-6 shadow-2xl space-y-5 transition-colors duration-200">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ti ti-wallet text-indigo-600 dark:text-indigo-400"></i> Catat Transaksi Arus Kas Baru
                    </h3>
                    <button @click="showTransactionModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="ti ti-x text-lg"></i></button>
                </div>

                <form method="POST" :action="txType === 'inflow' ? '{{ route('finance.inflow.store') }}' : '{{ route('finance.outflow.store') }}'" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Tipe Transaksi *</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="txType = 'inflow'" :class="txType === 'inflow' ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800'" class="py-2 text-xs rounded-lg transition">
                                <i class="ti ti-arrow-up-right"></i> Uang Masuk (Inflow)
                            </button>
                            <button type="button" @click="txType = 'outflow'" :class="txType === 'outflow' ? 'bg-rose-600 text-white font-semibold' : 'bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800'" class="py-2 text-xs rounded-lg transition">
                                <i class="ti ti-arrow-down-left"></i> Uang Keluar (Outflow)
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Nominal (Rp) *</label>
                            <input type="number" step="0.01" name="amount" required placeholder="500000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Tanggal *</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Sumber / Peruntukan Dana *</label>
                        <input type="text" name="source_destination" required placeholder="Contoh: Pembayaran Pelanggan / Gaji Mentor" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Kategori *</label>
                        <input type="text" name="category" required placeholder="Contoh: Operasional / Penjualan" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div x-show="txType === 'outflow'">
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Upload Bukti Struk (Optional)</label>
                        <input type="file" name="proof_file" accept="image/*,.pdf" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs rounded-lg px-2.5 py-1.5">
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button type="button" @click="showTransactionModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Modal Buat Order Baru -->
        <div x-show="showOrderModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="showOrderModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-xl p-6 shadow-2xl space-y-5 transition-colors duration-200">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ti ti-shopping-cart text-emerald-600 dark:text-emerald-400"></i> Buat Order Pesanan Klien Baru
                    </h3>
                    <button @click="showOrderModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="ti ti-x text-lg"></i></button>
                </div>

                <form method="POST" action="{{ route('orders.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Nama Klien / Instansi *</label>
                        <input type="text" name="client_name" required placeholder="Contoh: PT Synergy Media" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Rincian Barang / Jasa *</label>
                        <textarea name="details" required rows="2" placeholder="Tuliskan rincian item..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Total Nominal (Rp) *</label>
                            <input type="number" step="0.01" name="total_amount" required placeholder="10000000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Tanggal Transaksi *</label>
                            <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Status Pembayaran *</label>
                        <select name="payment_status" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                            <option value="pending">Pending (Belum Bayar)</option>
                            <option value="dp">DP (Uang Muka)</option>
                            <option value="lunas">Lunas (Otomatis Sync ke Arus Kas)</option>
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button type="button" @click="showOrderModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg shadow-sm">Simpan Order</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Modal Buat Jadwal Konten -->
        <div x-show="showContentModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="showContentModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-xl p-6 shadow-2xl space-y-5 transition-colors duration-200">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ti ti-calendar-plus text-purple-600 dark:text-purple-400"></i> Buat Agenda Konten Media Sosial
                    </h3>
                    <button @click="showContentModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="ti ti-x text-lg"></i></button>
                </div>

                <form method="POST" action="{{ route('content.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Judul / Topik Konten *</label>
                        <input type="text" name="title" required placeholder="Contoh: Showcase Portofolio Client Q3" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Platform *</label>
                            <select name="platform" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                                <option value="Instagram">Instagram</option>
                                <option value="TikTok">TikTok</option>
                                <option value="YouTube">YouTube</option>
                                <option value="LinkedIn">LinkedIn</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Status Workflow *</label>
                            <select name="status" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                                <option value="draft">Draft</option>
                                <option value="production">Produksi</option>
                                <option value="review">Review</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Jadwal Tayang *</label>
                            <input type="datetime-local" name="scheduled_at" required value="{{ date('Y-m-d\TH:i') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Penanggung Jawab (PIC)</label>
                            <select name="assigned_to" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                                <option value="">-- Pilih PIC --</option>
                                @foreach($usersList as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }} ({{ strtoupper($usr->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button type="button" @click="showContentModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-500 text-white text-xs font-semibold rounded-lg shadow-sm">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function dashboardApp() {
            return {
                showTransactionModal: false,
                showOrderModal: false,
                showContentModal: false,
                txType: 'inflow'
            }
        }
    </script>
</x-app-layout>
