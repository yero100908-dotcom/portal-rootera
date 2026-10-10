<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 w-full">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-500/20">
                        <i class="ti ti-sparkles text-xs"></i> Tugas & Penugasan
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="ti ti-notebook text-indigo-600 dark:text-indigo-400"></i>
                    Manajemen Tugas (Assignment)
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Modul instruksi tugas tim, unggah file tugas, repositori, dan rekapitulasi penilaian
                </p>
            </div>
            @if($isManager)
                <div class="shrink-0">
                    <a href="{{ route('assignments.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-semibold text-xs rounded-xl shadow-sm shadow-indigo-500/25 transition-all transform active:scale-95">
                        <i class="ti ti-plus text-base"></i>
                        <span>Buat Tugas Baru</span>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-5 sm:space-y-6" x-data="{ submitModalAssignment: null, selectedFileName: '', selectedFileSize: '' }">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                        <i class="ti ti-circle-check text-base"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                        <i class="ti ti-alert-circle text-base"></i>
                    </div>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Responsive Stats Overview Cards (2-col mobile, 4-col desktop) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4.5">
            @if($isManager)
                <!-- Manager Metric 1: Total Tugas -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-indigo-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Tugas</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti ti-files"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalAssignments }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">dibuat</span>
                    </div>
                </div>

                <!-- Manager Metric 2: Tugas Aktif -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-emerald-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Tugas Aktif</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti ti-clock-play"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $activeAssignments }}</h3>
                        <span class="text-[10px] text-emerald-500 font-medium">sedang berjalan</span>
                    </div>
                </div>

                <!-- Manager Metric 3: Pengumpulan Karyawan -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-blue-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Jawaban Masuk</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti ti-inbox"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">{{ $totalSubmissionsCount }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">berkas</span>
                    </div>
                </div>

                <!-- Manager Metric 4: Jawaban Dinilai -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-amber-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Sudah Dinilai</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti ti-award"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400 tracking-tight">{{ $totalGradedCount }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">tuntas</span>
                    </div>
                </div>
            @else
                <!-- Employee Metric 1: Tugas Belum Dikumpul -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-indigo-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Belum Kumpul</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl {{ $myPendingCount == 0 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400' }} flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti {{ $myPendingCount == 0 ? 'ti-check' : 'ti-alert-triangle' }}"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold {{ $myPendingCount == 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} tracking-tight">
                            {{ $myPendingCount }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium">tugas</span>
                    </div>
                </div>

                <!-- Employee Metric 2: Sudah Dikumpul -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-blue-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Sudah Dikumpul</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti ti-circle-check"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">{{ $mySubmittedCount }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">selesai</span>
                    </div>
                </div>

                <!-- Employee Metric 3: Nilai Selesai -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-purple-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Telah Dinilai</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti ti-award"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-purple-600 dark:text-purple-400 tracking-tight">{{ $myGradedCount }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">diberi skor</span>
                    </div>
                </div>

                <!-- Employee Metric 4: Total Tugas Aktif -->
                <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-indigo-500/30 transition-all duration-200 group">
                    <div class="flex items-center justify-between gap-2 mb-2 sm:mb-3">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Tugas Aktif</span>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base sm:text-lg shrink-0 group-hover:scale-105 transition-transform">
                            <i class="ti ti-files"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-xl sm:text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 tracking-tight">{{ $activeAssignments }}</h3>
                        <span class="text-[10px] text-slate-400 font-medium">periode aktif</span>
                    </div>
                </div>
            @endif
        </div>

        @if(!$isManager)
            <!-- Responsive Mobile-Friendly Scrollable Segmented Tabs for Employee -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
                <a href="{{ route('assignments.index', ['tab' => 'pending']) }}" class="shrink-0 inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 text-xs font-bold rounded-xl transition shadow-xs {{ $submissionFilter === 'pending' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-indigo-500/25' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-clock"></i>
                    <span>Belum Dikumpul</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full {{ $submissionFilter === 'pending' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">{{ $myPendingCount }}</span>
                </a>
                <a href="{{ route('assignments.index', ['tab' => 'submitted']) }}" class="shrink-0 inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 text-xs font-bold rounded-xl transition shadow-xs {{ $submissionFilter === 'submitted' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-indigo-500/25' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-circle-check"></i>
                    <span>Riwayat Pengumpulan</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full {{ $submissionFilter === 'submitted' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">{{ $mySubmittedCount }}</span>
                </a>
                <a href="{{ route('assignments.index', ['tab' => 'all']) }}" class="shrink-0 inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 text-xs font-bold rounded-xl transition shadow-xs {{ $submissionFilter === 'all' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-indigo-500/25' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-list"></i>
                    <span>Semua Tugas</span>
                </a>
            </div>
        @endif

        <!-- Search & Filter Controls -->
        <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-3.5 sm:p-4 shadow-xs">
            <form method="GET" action="{{ route('assignments.index') }}" class="flex flex-col sm:flex-row gap-2.5 sm:gap-3">
                <input type="hidden" name="tab" value="{{ $submissionFilter }}">
                <div class="flex-1 relative">
                    <i class="ti ti-search absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tugas, instruksi, atau kata kunci..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl pl-10 pr-3 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                </div>
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Tenggat Lewat</option>
                        @if($isManager)
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        @endif
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center justify-center gap-1.5 active:scale-95">
                        <i class="ti ti-filter"></i>
                        <span>Filter</span>
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('assignments.index', ['tab' => $submissionFilter]) }}" title="Reset Filter" class="p-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center justify-center">
                            <i class="ti ti-rotate-clockwise text-base"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Assignments Grid / Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @forelse($assignments as $ass)
                @php
                    $mySub = $userSubmissions->get($ass->id);
                    $subCount = $ass->submissions->count();
                    $isOverdue = $ass->isOverdue();
                    $myOverride = $ass->overrides ? $ass->overrides->firstWhere('user_id', Auth::id()) : null;
                    $hasCustomDeadline = $myOverride && $myOverride->is_unlocked && $myOverride->custom_due_date;
                    $effectiveDeadline = $hasCustomDeadline ? $myOverride->custom_due_date : $ass->deadline;
                    $isOverdueForMe = now()->greaterThan($effectiveDeadline);
                @endphp
                <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-indigo-500/40 transition-all duration-200 flex flex-col justify-between space-y-4 group">
                    <div>
                        <!-- Header Status Badge -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg border
                                @if(!$ass->is_active) bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20
                                @elseif(!$isManager && $hasCustomDeadline && !$isOverdueForMe) bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-500/20
                                @elseif(!$isManager ? $isOverdueForMe : $isOverdue) bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20
                                @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                                <span class="w-1.5 h-1.5 rounded-full 
                                    @if(!$ass->is_active) bg-slate-400
                                    @elseif(!$isManager && $hasCustomDeadline && !$isOverdueForMe) bg-indigo-500 animate-pulse
                                    @elseif(!$isManager ? $isOverdueForMe : $isOverdue) bg-rose-500 animate-pulse
                                    @else bg-emerald-500 animate-pulse @endif"></span>
                                @if(!$ass->is_active) Non-Aktif
                                @elseif(!$isManager && $hasCustomDeadline && !$isOverdueForMe) Diperpanjang
                                @elseif(!$isManager ? $isOverdueForMe : $isOverdue) Tenggat Lewat
                                @else Aktif @endif
                            </span>

                            @if(!$isManager)
                                @if($mySub)
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border flex items-center gap-1
                                        @if($mySub->isGraded())
                                            @if($mySub->isRevisionNeeded()) bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                            @else bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20 @endif
                                        @elseif($mySub->isLate()) bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                        @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                                        <i class="ti {{ $mySub->isRevisionNeeded() ? 'ti-refresh' : ($mySub->isGraded() ? 'ti-award' : 'ti-circle-check') }} text-xs"></i>
                                        @if($mySub->isGraded())
                                            @if($mySub->isRevisionNeeded())
                                                Perlu Revisi (Nilai: {{ $mySub->score }}/100)
                                            @else
                                                Sudah Dinilai (Nilai: {{ $mySub->score }}/100)
                                            @endif
                                        @elseif($mySub->isLate())
                                            Sudah Dikumpul (Terlambat)
                                        @else
                                            Sudah Dikumpul
                                        @endif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-semibold text-rose-600 dark:text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-lg flex items-center gap-1">
                                        <i class="ti ti-clock text-xs"></i>
                                        Belum Dikumpul
                                    </span>
                                @endif
                            @endif
                        </div>

                        <!-- Title & Description -->
                        <h2 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-snug line-clamp-2">
                            <a href="{{ route('assignments.show', $ass->id) }}">{{ $ass->title }}</a>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ $ass->description }}
                        </p>

                        @if(!$isManager && $hasCustomDeadline)
                            <div class="mt-2.5 px-3 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-xs flex items-center justify-between gap-2">
                                <span class="text-indigo-700 dark:text-indigo-300 font-semibold flex items-center gap-1.5">
                                    <i class="ti ti-calendar-time text-sm"></i>
                                    <span>Tenggat Khusus Anda:</span>
                                </span>
                                <span class="font-mono font-bold {{ $isOverdueForMe ? 'text-rose-600 dark:text-rose-400' : 'text-indigo-700 dark:text-indigo-300' }}">
                                    {{ $myOverride->custom_due_date->translatedFormat('d M, H:i') }}
                                </span>
                            </div>
                        @endif

                        @if($mySub && $mySub->feedback)
                            <div class="mt-2.5 p-2.5 rounded-xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/80 dark:border-purple-800/60 text-xs space-y-0.5">
                                <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wider flex items-center gap-1">
                                    <i class="ti ti-award"></i> Catatan Mentor:
                                </span>
                                <p class="text-[11px] text-slate-700 dark:text-slate-300 italic line-clamp-2">"{{ $mySub->feedback }}"</p>
                            </div>
                        @endif
                    </div>

                    <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800/80 space-y-2.5">
                        <!-- Info Meta: Creator & Deadline -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 gap-2">
                            <span class="flex items-center gap-1.5 truncate">
                                <i class="ti ti-user-circle text-sm text-indigo-500"></i>
                                <span class="truncate">{{ $ass->creator->name ?? 'Admin' }}</span>
                            </span>
                            <span class="flex items-center gap-1 font-mono shrink-0 {{ $isOverdueForMe ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">
                                <i class="ti ti-calendar-time text-xs"></i>
                                <span>{{ $effectiveDeadline->format('d M, H:i') }}</span>
                            </span>
                        </div>

                        @if($isManager)
                            <div class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 flex items-center justify-between bg-slate-50 dark:bg-slate-950/80 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-slate-800/80">
                                <span>Total Mengumpulkan:</span>
                                <span class="font-mono text-indigo-600 dark:text-indigo-400 font-bold">{{ $subCount }} Karyawan</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between gap-2 pt-1">
                            @php
                                $isMentorOrOwner = Auth::user()->isOwner() || Auth::user()->isMentor() || Auth::user()->hasAnyRole(['owner', 'mentor']);
                            @endphp

                            @if($isMentorOrOwner)
                                <div class="flex items-center gap-2 flex-1">
                                    <a href="{{ route('assignments.submissions', $ass->id) }}" class="flex-1 px-3.5 py-2.5 sm:py-2 text-xs font-bold rounded-xl transition text-center flex items-center justify-center gap-1.5 active:scale-95 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white shadow-xs shadow-indigo-500/25">
                                        <i class="ti ti-checklist text-base"></i>
                                        <span>Periksa Pengumpulan ({{ $ass->submissions_count }} Jawaban)</span>
                                    </a>
                                    <a href="{{ route('assignments.show', $ass->id) }}" title="Lihat Petunjuk Tugas" class="p-2.5 sm:p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center justify-center">
                                        <i class="ti ti-file-description text-sm"></i>
                                    </a>
                                </div>
                            @elseif(!$isManager && !$mySub)
                                <div class="flex items-center gap-2 flex-1">
                                    <button type="button" @click="submitModalAssignment = { id: {{ $ass->id }}, title: '{{ addslashes($ass->title) }}', deadline: '{{ $effectiveDeadline->format('d M, H:i') }}' }; selectedFileName = ''; selectedFileSize = '';" class="flex-1 px-3.5 py-2.5 sm:py-2 text-xs font-bold rounded-xl transition text-center flex items-center justify-center gap-1.5 active:scale-95 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white shadow-xs shadow-indigo-500/25">
                                        <i class="ti ti-upload"></i>
                                        <span>Kirim Tugas</span>
                                    </button>
                                    <a href="{{ route('assignments.show', $ass->id) }}" title="Lihat Petunjuk Lengkap" class="p-2.5 sm:p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center justify-center">
                                        <i class="ti ti-file-description text-sm"></i>
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('assignments.show', $ass->id) }}" class="flex-1 px-3.5 py-2.5 sm:py-2 text-xs font-bold rounded-xl transition text-center flex items-center justify-center gap-1.5 active:scale-95 {{ $mySub ? 'bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white shadow-xs shadow-indigo-500/25' }}">
                                    <i class="ti {{ $mySub ? 'ti-eye' : 'ti-upload' }}"></i>
                                    <span>{{ $mySub ? 'Detail & Jawaban' : 'Kerjakan Sekarang' }}</span>
                                </a>
                            @endif

                            @if($isManager)
                                <a href="{{ route('assignments.edit', $ass->id) }}" title="Edit Tugas" class="p-2.5 sm:p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition flex items-center justify-center">
                                    <i class="ti ti-edit text-sm"></i>
                                </a>

                                <form method="POST" action="{{ route('assignments.destroy', $ass->id) }}" class="inline" onsubmit="return confirm('Hapus tugas ini beserta seluruh pengumpulannya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Tugas" class="p-2.5 sm:p-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-xl border border-rose-500/20 transition flex items-center justify-center">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-8 sm:p-12 text-center space-y-4 shadow-xs">
                    @if(!$isManager && $submissionFilter === 'pending')
                        <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-3xl shadow-xs">
                            <i class="ti ti-circle-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Semua Tugas Sudah Dikumpulkan!</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto leading-relaxed">
                                Luar biasa! Tidak ada tugas aktif yang belum Anda kumpulkan. Anda dapat meninjau hasil nilai dan umpan balik di tab riwayat pengumpulan.
                            </p>
                        </div>
                        <a href="{{ route('assignments.index', ['tab' => 'submitted']) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-500/25 transition active:scale-95">
                            <i class="ti ti-history"></i>
                            <span>Buka Riwayat Tugas Selesai</span>
                        </a>
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-3xl">
                            <i class="ti ti-clipboard-x"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Belum Ada Tugas Ditampilkan</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto leading-relaxed">
                                Belum ada data tugas yang sesuai dengan pencarian atau filter yang Anda pilih saat ini.
                            </p>
                        </div>
                        @if($isManager)
                            <a href="{{ route('assignments.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                                <i class="ti ti-plus"></i>
                                <span>Buat Tugas Pertama</span>
                            </a>
                        @endif
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $assignments->links() }}
        </div>

        <!-- Google Classroom-style Quick Submission Modal -->
        <div x-show="submitModalAssignment !== null" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="submitModalAssignment = null" 
                 x-show="submitModalAssignment !== null"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 max-w-lg w-full text-left space-y-4 shadow-2xl">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                            <i class="ti ti-upload"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white" x-text="submitModalAssignment?.title ?? 'Pengumpulan Tugas'"></h3>
                            <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                <i class="ti ti-calendar-time text-xs"></i>
                                <span>Tenggat: <strong class="text-slate-700 dark:text-slate-300" x-text="submitModalAssignment?.deadline"></strong></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="submitModalAssignment = null" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>

                <form method="POST" :action="'/assignments/' + submitModalAssignment?.id + '/submit'" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Unggah Berkas File (Opsional, Maks 20MB)
                        </label>
                        <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 rounded-xl p-4 text-center transition bg-slate-50/50 dark:bg-slate-950/40">
                            <input type="file" name="submission_file" 
                                   @change="if($event.target.files.length > 0) { selectedFileName = $event.target.files[0].name; selectedFileSize = ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB'; }"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="space-y-1 pointer-events-none">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto text-base">
                                    <i class="ti ti-cloud-upload"></i>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">Pilih berkas</span> atau seret file ke sini
                                </div>
                                <p class="text-[10px] text-slate-400">PDF, DOCX, ZIP, Gambar</p>
                            </div>
                        </div>

                        <template x-if="selectedFileName">
                            <div class="mt-2 p-2.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-500/20 flex items-center justify-between text-xs text-indigo-700 dark:text-indigo-300 font-semibold">
                                <div class="flex items-center gap-2 truncate">
                                    <i class="ti ti-file-check text-base shrink-0"></i>
                                    <span class="truncate" x-text="selectedFileName"></span>
                                    <span class="text-[10px] font-mono text-indigo-500" x-text="'(' + selectedFileSize + ')'"></span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase">Siap</span>
                            </div>
                        </template>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Tautan URL Tugas / Repositori (Opsional)
                        </label>
                        <div class="relative">
                            <i class="ti ti-link absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                            <input type="url" name="submission_url" placeholder="https://github.com/... atau link Google Drive" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl pl-9 pr-3 py-2 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Catatan Pengerjaan (Opsional)
                        </label>
                        <textarea name="notes" rows="2" placeholder="Catatan singkat untuk mentor..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                        <a :href="'/assignments/' + submitModalAssignment?.id" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                            <span>Buka Panduan Lengkap</span>
                            <i class="ti ti-arrow-right text-xs"></i>
                        </a>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="submitModalAssignment = null; selectedFileName = ''; selectedFileSize = '';" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white text-xs font-semibold rounded-xl shadow-xs transition active:scale-95 flex items-center gap-1.5">
                                <i class="ti ti-send text-xs"></i>
                                <span>Kirim Tugas</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
