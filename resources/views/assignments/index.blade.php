<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="ti ti-notebook text-indigo-600 dark:text-indigo-400"></i>
                    Sistem Manajemen Tugas (Assignment)
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Modul instruksi tugas, pengumpulan dokumen/repositori, penilaian, dan umpan balik tim
                </p>
            </div>
            @if($isManager)
                <div>
                    <a href="{{ route('assignments.create') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <i class="ti ti-plus text-base"></i> Buat Tugas Baru
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-xl text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ti ti-circle-check text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-xl text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ti ti-alert-circle text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($isManager)
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tugas</p>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalAssignments }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-files"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tugas Aktif</p>
                            <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $activeAssignments }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-clock-play"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Jawaban Karyawan</p>
                            <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $totalSubmissionsCount }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Jawaban Dinilai</p>
                            <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $totalGradedCount }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-award"></i>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tugas Belum Dikumpul</p>
                            <h3 class="text-2xl font-bold {{ $myPendingCount == 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} mt-1">
                                {{ $myPendingCount }}
                            </h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg {{ $myPendingCount == 0 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400' }} flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-alert-triangle"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tugas Saya Dikumpul</p>
                            <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $mySubmittedCount }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nilai Selesai</p>
                            <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $myGradedCount }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-award"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tugas Aktif</p>
                            <h3 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $activeAssignments }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-bold">
                            <i class="ti ti-files"></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if(!$isManager)
            <!-- Tabs Navigation for Employee -->
            <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                <a href="{{ route('assignments.index', ['tab' => 'pending']) }}" class="px-3.5 py-2 text-xs font-bold rounded-lg transition flex items-center gap-1.5 {{ $submissionFilter === 'pending' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-clock"></i> Belum Dikumpul (Aktif)
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $submissionFilter === 'pending' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $myPendingCount }}</span>
                </a>
                <a href="{{ route('assignments.index', ['tab' => 'submitted']) }}" class="px-3.5 py-2 text-xs font-bold rounded-lg transition flex items-center gap-1.5 {{ $submissionFilter === 'submitted' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-circle-check"></i> Riwayat Sudah Dikumpul
                    <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $submissionFilter === 'submitted' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $mySubmittedCount }}</span>
                </a>
                <a href="{{ route('assignments.index', ['tab' => 'all']) }}" class="px-3.5 py-2 text-xs font-bold rounded-lg transition flex items-center gap-1.5 {{ $submissionFilter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-list"></i> Semua Tugas
                </a>
            </div>
        @endif

        <!-- Search & Filter Bar -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('assignments.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="tab" value="{{ $submissionFilter }}">
                <div class="flex-1">
                    <div class="relative">
                        <i class="ti ti-search absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tugas atau instruksi..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg pl-9 pr-3 py-2 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Semua Status --</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Tenggat Waktu Lewat</option>
                        @if($isManager)
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        @endif
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-sm flex items-center justify-center gap-1.5">
                    <i class="ti ti-filter"></i> Filter
                </button>
            </form>
        </div>

        <!-- Assignments Grid / Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($assignments as $ass)
                @php
                    $mySub = $userSubmissions->get($ass->id);
                    $subCount = $ass->submissions->count();
                    $isOverdue = $ass->isOverdue();
                @endphp
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                    <div>
                        <!-- Header Status Badge -->
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md border
                                @if(!$ass->is_active) bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20
                                @elseif($isOverdue) bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20
                                @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                                @if(!$ass->is_active) Non-Aktif
                                @elseif($isOverdue) Tenggat Lewat
                                @else Aktif @endif
                            </span>

                            @if($mySub)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded border flex items-center gap-1
                                    @if($mySub->isGraded()) bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20
                                    @elseif($mySub->isLate()) bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                    @else bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20 @endif">
                                    <i class="ti ti-check text-xs"></i>
                                    @if($mySub->isGraded()) Nilai: {{ $mySub->score }}/100
                                    @elseif($mySub->isLate()) Terlambat
                                    @else Dikumpulkan @endif
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-medium text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 rounded">
                                    Belum Dikumpul
                                </span>
                            @endif
                        </div>

                        <!-- Title & Description -->
                        <h2 class="text-base font-bold text-slate-900 dark:text-white hover:text-indigo-600 transition leading-snug">
                            <a href="{{ route('assignments.show', $ass->id) }}">{{ $ass->title }}</a>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                            {{ $ass->description }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/60 space-y-2">
                        <!-- Info Meta -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span class="flex items-center gap-1">
                                <i class="ti ti-user text-xs text-indigo-500"></i> {{ $ass->creator->name ?? 'Admin' }}
                            </span>
                            <span class="flex items-center gap-1 font-mono {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-semibold' : '' }}">
                                <i class="ti ti-calendar text-xs"></i> {{ $ass->deadline->format('d M Y, H:i') }}
                            </span>
                        </div>

                        @if($isManager)
                            <div class="text-[11px] font-semibold text-slate-600 dark:text-slate-300 flex items-center justify-between bg-slate-50 dark:bg-slate-950 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800">
                                <span>Total Mengumpulkan:</span>
                                <span class="font-mono text-indigo-600 dark:text-indigo-400 font-bold">{{ $subCount }} Karyawan</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between gap-2 pt-1">
                            <a href="{{ route('assignments.show', $ass->id) }}" class="flex-1 px-3 py-1.5 bg-indigo-600/10 hover:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 text-xs font-semibold rounded-lg border border-indigo-500/20 transition text-center flex items-center justify-center gap-1">
                                <i class="ti {{ $mySub ? 'ti-eye' : 'ti-upload' }}"></i> {{ $mySub ? 'Detail & Jawaban' : 'Kerjakan & Kumpulkan' }}
                            </a>

                            @if($isManager)
                                <a href="{{ route('assignments.edit', $ass->id) }}" title="Edit" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                                    <i class="ti ti-edit"></i>
                                </a>

                                <form method="POST" action="{{ route('assignments.destroy', $ass->id) }}" class="inline" onsubmit="return confirm('Hapus tugas ini beserta seluruh pengumpulannya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-lg border border-rose-500/20 transition">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-12 text-center space-y-3">
                    @if(!$isManager && $submissionFilter === 'pending')
                        <div class="w-14 h-14 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-3xl">
                            <i class="ti ti-circle-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Semua Tugas Sudah Dikumpulkan!</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                                Tidak ada tugas aktif yang belum dikumpulkan. Tugas yang sudah Anda kumpulkan otomatis dipindahkan ke tab riwayat.
                            </p>
                        </div>
                        <a href="{{ route('assignments.index', ['tab' => 'submitted']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            <i class="ti ti-history"></i> Lihat Riwayat Tugas Dikumpul
                        </a>
                    @else
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                            <i class="ti ti-clipboard-x"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Tugas Ditampilkan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                            Belum ada tugas yang cocok dengan filter atau belum ada tugas baru yang dibuat oleh mentor/admin.
                        </p>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $assignments->links() }}
        </div>
    </div>
</x-app-layout>
