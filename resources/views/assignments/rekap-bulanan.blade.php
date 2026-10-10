<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mb-1.5">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Beranda</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <a href="{{ route('assignments.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Manajemen Tugas</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-900 dark:text-white font-medium">Rekap Tugas Bulanan</span>
                </nav>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                        <i class="ti ti-report-analytics text-xl"></i>
                    </span>
                    Rekap Tugas Bulanan (Monthly Report)
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Laporan kepatuhan pengumpulan tugas, analisis performa anggota tim, dan statistik penyelesaian tugas bulanan.
                </p>
            </div>

            <!-- Action Buttons Header -->
            <div class="flex items-center flex-wrap gap-2 print:hidden">
                <a href="{{ route('assignments.index') }}" 
                   class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5 shadow-xs">
                    <i class="ti ti-arrow-left text-sm"></i>
                    <span>Kembali ke Tugas</span>
                </a>

                <button type="button" 
                        onclick="window.print()" 
                        class="px-3 py-2 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition flex items-center gap-1.5 shadow-xs">
                    <i class="ti ti-printer text-sm"></i>
                    <span>Cetak Rekap</span>
                </button>

                <a href="{{ route('assignments.rekap-bulanan.export', ['month' => $month, 'year' => $year]) }}" 
                   class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <i class="ti ti-file-spreadsheet text-base"></i>
                    <span>Ekspor CSV / Excel</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        search: '',
        modalOpen: false,
        selectedMember: null,
        openDetail(member) {
            this.selectedMember = member;
            this.modalOpen = true;
        }
    }">

        <!-- Filter Periode Bar -->
        <div class="bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs transition-colors duration-200 print:hidden">
            <form method="GET" action="{{ route('assignments.rekap-bulanan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-calendar text-sm text-indigo-500"></i> Pilih Bulan
                    </label>
                    <select name="month" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs font-medium rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @foreach($monthsList as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-calendar-event text-sm text-indigo-500"></i> Pilih Tahun
                    </label>
                    <select name="year" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs font-medium rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @foreach(range(date('Y') - 3, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                Tahun {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" 
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center justify-center gap-1.5">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    @if($month != date('n') || $year != date('Y'))
                        <a href="{{ route('assignments.rekap-bulanan') }}" 
                           class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl transition border border-slate-200 dark:border-slate-700 shrink-0" 
                           title="Reset ke Bulan Berjalan">
                            <i class="ti ti-rotate-clockwise text-base"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Tugas Diterbitkan -->
            <div class="bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-xs hover:border-indigo-500/30 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Tugas Diterbitkan</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                        <i class="ti ti-notebook text-lg"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-baseline gap-1.5">
                    <span>{{ $totalAssignments }}</span>
                    <span class="text-xs font-normal text-slate-500">Tugas</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1">
                    <i class="ti ti-calendar text-xs"></i>
                    <span>Periode {{ $monthsList[$month] ?? $month }} {{ $year }}</span>
                </p>
            </div>

            <!-- Total Jawaban Masuk -->
            <div class="bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-xs hover:border-emerald-500/30 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Jawaban Masuk</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                        <i class="ti ti-file-check text-lg"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight flex items-baseline gap-1.5">
                    <span>{{ $totalSubmissions }}</span>
                    <span class="text-xs font-normal text-slate-500">Submissions</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1">
                    <i class="ti ti-users text-xs"></i>
                    <span>Akumulasi dari seluruh anggota tim</span>
                </p>
            </div>

            <!-- Tingkat Penyelesaian Tugas Tim -->
            <div class="bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-xs hover:border-blue-500/30 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Penyelesaian Tim</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-500/20">
                        <i class="ti ti-chart-pie text-lg"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 tracking-tight flex items-baseline gap-1.5">
                    <span>{{ $submissionRate }}%</span>
                    <span class="text-xs font-normal text-slate-500">Kepatuhan</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2.5 overflow-hidden">
                    <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $submissionRate) }}%"></div>
                </div>
            </div>

            <!-- Total Tugas Terlambat -->
            <div class="bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 rounded-2xl p-5 shadow-xs hover:border-amber-500/30 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tugas Terlambat</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20">
                        <i class="ti ti-clock-alert text-lg"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold {{ $totalLateSubmissions > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-white' }} tracking-tight flex items-baseline gap-1.5">
                    <span>{{ $totalLateSubmissions }}</span>
                    <span class="text-xs font-normal text-slate-500">Tugas Terlambat</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1">
                    <i class="ti ti-alert-triangle text-xs"></i>
                    <span>Melewati batas deadline tugas</span>
                </p>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Penugasan Anggota / Karyawan -->
        <div class="bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-xs overflow-hidden transition-colors duration-200">
            <!-- Table Header Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                        <i class="ti ti-users-group text-base"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span>Performa Penugasan Anggota Tim</span>
                            <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                {{ $memberReports->count() }} Anggota
                            </span>
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Rangkuman pengumpulan tugas tepat waktu, terlambat, dan belum dikerjakan</p>
                    </div>
                </div>

                <!-- Live Filter Search Box -->
                <div class="w-full sm:w-64 print:hidden">
                    <div class="relative">
                        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" 
                               x-model="search" 
                               placeholder="Cari nama karyawan..." 
                               class="w-full pl-9 pr-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
            </div>

            <!-- Table Responsive Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/75 dark:bg-slate-950/50 border-b border-slate-200 dark:border-slate-800/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Anggota & Role</th>
                            <th class="py-3 px-4 text-center">Ditugaskan</th>
                            <th class="py-3 px-4">Tugas Diselesaikan</th>
                            <th class="py-3 px-4 text-center">Belum Kumpul</th>
                            <th class="py-3 px-4 min-w-[160px]">Kepatuhan (%)</th>
                            <th class="py-3 px-4 text-center print:hidden">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                        @forelse($memberReports as $idx => $report)
                            @php
                                $u = $report['user'];
                                $searchString = strtolower($u->name . ' ' . $u->email . ' ' . $u->role);
                                $reportJson = json_encode([
                                    'name' => $u->name,
                                    'email' => $u->email,
                                    'role' => strtoupper($u->role),
                                    'total_assigned' => $report['total_assigned'],
                                    'submitted_count' => $report['submitted_count'],
                                    'ontime_count' => $report['ontime_count'],
                                    'late_count' => $report['late_count'],
                                    'missing_count' => $report['missing_count'],
                                    'compliance_rate' => $report['compliance_rate'],
                                    'details' => $report['details'],
                                ]);
                            @endphp
                            <tr x-show="!search || '{{ $searchString }}'.includes(search.toLowerCase())"
                                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- No -->
                                <td class="py-3.5 px-4 text-center font-medium text-slate-400 dark:text-slate-500">
                                    {{ $idx + 1 }}
                                </td>

                                <!-- Nama & Role -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                                {{ $u->name }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $u->email }}</span>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                    {{ $u->role }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Total Ditugaskan -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $report['total_assigned'] }}
                                    </span>
                                </td>

                                <!-- Tugas Diselesaikan -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center flex-wrap gap-1.5">
                                        @if($report['submitted_count'] > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                                <i class="ti ti-check text-xs"></i>
                                                {{ $report['ontime_count'] }} Tepat Waktu
                                            </span>
                                            @if($report['late_count'] > 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                                    <i class="ti ti-clock-alert text-xs"></i>
                                                    {{ $report['late_count'] }} Terlambat
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-[11px] text-slate-400 italic">Belum ada tugas selesai</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Belum Dikumpulkan -->
                                <td class="py-3.5 px-4 text-center">
                                    @if($report['missing_count'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/20">
                                            <i class="ti ti-x text-xs"></i>
                                            {{ $report['missing_count'] }} Terlewat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                            <i class="ti ti-circle-check text-sm"></i> Tuntas
                                        </span>
                                    @endif
                                </td>

                                <!-- Persentase Kepatuhan -->
                                <td class="py-3.5 px-4">
                                    @php
                                        $rate = $report['compliance_rate'];
                                        $colorClass = $rate >= 80 ? 'bg-emerald-500' : ($rate >= 50 ? 'bg-amber-500' : 'bg-rose-500');
                                        $textColorClass = $rate >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($rate >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400');
                                    @endphp
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold {{ $textColorClass }}">{{ $rate }}%</span>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $report['submitted_count'] }}/{{ $report['total_assigned'] }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $colorClass }} h-1.5 rounded-full transition-all duration-300" style="width: {{ $rate }}%"></div>
                                    </div>
                                </td>

                                <!-- Tombol Aksi: Lihat Detail -->
                                <td class="py-3.5 px-4 text-center print:hidden">
                                    <button type="button" 
                                            @click='openDetail({!! $reportJson !!})'
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-600/15 dark:hover:bg-indigo-600/30 text-indigo-700 dark:text-indigo-400 text-xs font-semibold rounded-lg border border-indigo-200 dark:border-indigo-500/30 transition shadow-xs">
                                        <i class="ti ti-eye text-sm"></i>
                                        <span>Lihat Detail</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <i class="ti ti-users-minus text-2xl"></i>
                                    </div>
                                    <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">Tidak ada anggota yang dievaluasi</p>
                                    <p class="text-xs text-slate-400 mt-1">Belum ada karyawan terdaftar dalam sistem.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Summary -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <i class="ti ti-info-circle text-indigo-500"></i>
                    <span>Tugas yang diperhitungkan adalah tugas yang terbit pada rentang <strong>{{ $monthsList[$month] ?? $month }} {{ $year }}</strong>.</span>
                </div>
                <div>
                    Menampilkan <strong>{{ $memberReports->count() }}</strong> anggota tim
                </div>
            </div>
        </div>

        <!-- Alpine.js Modal Detail Penugasan Anggota -->
        <div x-show="modalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" 
                 @click="modalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-left shadow-2xl transition-all w-full max-w-2xl p-6 sm:p-7"
                     @click.stop>
                    
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-600 text-white font-bold flex items-center justify-center text-sm shadow-xs">
                                <span x-text="selectedMember?.name ? selectedMember.name.charAt(0).toUpperCase() : 'U'"></span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="selectedMember?.name"></h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-mono" x-text="selectedMember?.email"></span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300" x-text="selectedMember?.role"></span>
                                </div>
                            </div>
                        </div>
                        <button type="button" 
                                @click="modalOpen = false" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <i class="ti ti-x text-lg"></i>
                        </button>
                    </div>

                    <!-- Member Quick Metric Pills in Modal -->
                    <div class="grid grid-cols-3 gap-2.5 my-4">
                        <div class="bg-slate-50 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Tugas</span>
                            <span class="text-base font-bold text-slate-900 dark:text-white" x-text="selectedMember?.total_assigned"></span>
                        </div>
                        <div class="bg-emerald-500/10 p-2.5 rounded-xl border border-emerald-500/20 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Diselesaikan</span>
                            <span class="text-base font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedMember?.submitted_count"></span>
                        </div>
                        <div class="bg-indigo-500/10 p-2.5 rounded-xl border border-indigo-500/20 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 block">Kepatuhan</span>
                            <span class="text-base font-bold text-indigo-600 dark:text-indigo-400" x-text="selectedMember?.compliance_rate + '%'"></span>
                        </div>
                    </div>

                    <!-- Assignment List in Modal -->
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="ti ti-list-check text-indigo-500"></i> Rincian Tugas Bulan {{ $monthsList[$month] ?? $month }} {{ $year }}
                        </h4>

                        <template x-if="selectedMember?.details && selectedMember.details.length > 0">
                            <div class="space-y-2">
                                <template x-for="(item, idx) in selectedMember.details" :key="idx">
                                    <div class="p-3 rounded-xl border transition-colors flex items-center justify-between gap-3"
                                         :class="item.is_submitted ? 'bg-slate-50/50 dark:bg-slate-950/40 border-slate-200 dark:border-slate-800' : 'bg-rose-500/5 dark:bg-rose-500/10 border-rose-200 dark:border-rose-900/30'">
                                        <div class="min-w-0">
                                            <div class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="item.title"></div>
                                            <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                                <span>Deadline: <strong class="text-slate-700 dark:text-slate-300" x-text="item.deadline"></strong></span>
                                                <span x-show="item.submitted_at">&bull; Dikumpul: <span x-text="item.submitted_at"></span></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <!-- Status Pill -->
                                            <template x-if="item.is_submitted && !item.is_late">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                                    <i class="ti ti-check"></i> Tepat Waktu
                                                </span>
                                            </template>
                                            <template x-if="item.is_submitted && item.is_late">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                                    <i class="ti ti-clock-alert"></i> Terlambat
                                                </span>
                                            </template>
                                            <template x-if="!item.is_submitted">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/20">
                                                    <i class="ti ti-x"></i> Belum Kumpul
                                                </span>
                                            </template>

                                            <!-- Nilai Score if exists -->
                                            <template x-if="item.score !== null">
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 dark:bg-indigo-600/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30">
                                                    Nilai: <span x-text="item.score"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!selectedMember?.details || selectedMember.details.length === 0">
                            <div class="py-6 text-center text-xs text-slate-400">
                                Tidak ada penugasan yang diterbitkan pada bulan ini.
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                        <button type="button" 
                                @click="modalOpen = false" 
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Print CSS Optimization -->
    <style>
        @media print {
            aside, header, nav, .print\:hidden {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .min-h-screen {
                min-height: auto !important;
            }
        }
    </style>
</x-app-layout>
