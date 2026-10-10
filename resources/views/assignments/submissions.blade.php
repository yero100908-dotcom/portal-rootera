<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 w-full">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <a href="{{ route('assignments.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white bg-slate-100 dark:bg-slate-800/80 transition group">
                        <i class="ti ti-arrow-left group-hover:-translate-x-0.5 transition-transform"></i>
                        <span>Kembali ke Manajemen Tugas</span>
                    </a>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-500/20">
                        <i class="ti ti-checklist text-xs"></i> Pengecekan Tugas & Riwayat
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border
                        @if(!$assignment->is_active) bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20
                        @elseif($assignment->isOverdue()) bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20
                        @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                        <span class="w-1.5 h-1.5 rounded-full 
                            @if(!$assignment->is_active) bg-slate-400
                            @elseif($assignment->isOverdue()) bg-rose-500 animate-pulse
                            @else bg-emerald-500 animate-pulse @endif"></span>
                        @if(!$assignment->is_active) Non-Aktif
                        @elseif($assignment->isOverdue()) Tenggat Lewat
                        @else Aktif @endif
                    </span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2 truncate">
                    <i class="ti ti-notebook text-indigo-600 dark:text-indigo-400 shrink-0"></i>
                    <span class="truncate">{{ $assignment->title }}</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Dibuat oleh <strong class="text-slate-700 dark:text-slate-200 font-semibold">{{ $assignment->creator->name ?? 'Mentor' }}</strong> &bull; Tenggat: <span class="font-mono font-semibold {{ $assignment->isOverdue() ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $assignment->deadline->format('d M Y, H:i') }} WIB</span>
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('assignments.show', $assignment->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-xl border border-slate-200/80 dark:border-slate-700 transition">
                    <i class="ti ti-eye text-sm"></i>
                    <span>Pratinjau Tugas</span>
                </a>
                <a href="{{ route('assignments.edit', $assignment->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-xl border border-slate-200/80 dark:border-slate-700 transition">
                    <i class="ti ti-edit text-sm"></i>
                    <span>Edit Tugas</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5 sm:space-y-6" x-data="{ 
        activeTab: 'all', 
        searchQuery: '',
        openGradeModal: false, 
        currentSubId: null,
        currentUserName: '',
        currentScore: 85,
        currentFeedback: '',
        currentStatus: 'graded',
        isSavingGrade: false,
        gradeError: '',
        async submitGrade(event) {
            if (!this.currentSubId) return;
            this.isSavingGrade = true;
            this.gradeError = '';
            try {
                const formData = new FormData(event.target);
                const response = await fetch('/submissions/' + this.currentSubId + '/grade', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    this.openGradeModal = false;
                    window.location.reload();
                } else {
                    this.gradeError = data.message || 'Gagal menyimpan nilai. Silakan coba lagi.';
                    this.isSavingGrade = false;
                }
            } catch (err) {
                event.target.submit();
            }
        },
        openExtendModal: false,
        extendUserId: null,
        extendUserName: '',
        extendCustomDueDate: '{{ now()->addDays(2)->format('Y-m-d\TH:i') }}',
        extendReason: '',
        previewImageSrc: null,
        previewImageTitle: ''
    }">
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

        <!-- Summary Statistics Matrix (5-Cards Responsive Grid) -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
            <!-- Metric 1: Total Pengumpulan -->
            <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 shadow-xs hover:border-indigo-500/30 transition group">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Sudah Mengumpulkan</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base shrink-0 group-hover:scale-105 transition-transform">
                        <i class="ti ti-inbox"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 tracking-tight">{{ $submittedCount }}</h3>
                    <span class="text-[10px] text-slate-400 font-medium">/ {{ $totalAssignees }} Anggota</span>
                </div>
                @php
                    $percent = $totalAssignees > 0 ? round(($submittedCount / $totalAssignees) * 100) : 0;
                @endphp
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mt-2.5 overflow-hidden">
                    <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                </div>
            </div>

            <!-- Metric 2: Belum Kumpul -->
            <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 shadow-xs hover:border-rose-500/30 transition group">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Belum Kumpul</span>
                    <div class="w-8 h-8 rounded-xl {{ $unsubmittedUsers->count() > 0 ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center text-base shrink-0 group-hover:scale-105 transition-transform">
                        <i class="ti {{ $unsubmittedUsers->count() > 0 ? 'ti-alert-triangle' : 'ti-circle-check' }}"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-xl sm:text-2xl font-extrabold {{ $unsubmittedUsers->count() > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} tracking-tight">
                        {{ $unsubmittedUsers->count() }}
                    </h3>
                    <span class="text-[10px] text-slate-400 font-medium">orang</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-2">
                    {{ $unsubmittedUsers->count() > 0 ? 'Menunggu pengumpulan' : 'Semua telah mengumpulkan' }}
                </p>
            </div>

            <!-- Metric 3: Menunggu Penilaian -->
            <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 shadow-xs hover:border-amber-500/30 transition group">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Perlu Dinilai</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base shrink-0 group-hover:scale-105 transition-transform">
                        <i class="ti ti-clock-pause"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400 tracking-tight">{{ $ungradedCount }}</h3>
                    <span class="text-[10px] text-slate-400 font-medium">berkas</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-2">
                    {{ $ungradedCount > 0 ? 'Siap untuk diperiksa mentor' : 'Semua jawaban dinilai' }}
                </p>
            </div>

            <!-- Metric 4: Sudah Dinilai -->
            <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 shadow-xs hover:border-purple-500/30 transition group">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Sudah Dinilai</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-base shrink-0 group-hover:scale-105 transition-transform">
                        <i class="ti ti-award"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-purple-600 dark:text-purple-400 tracking-tight">{{ $gradedCount }}</h3>
                    <span class="text-[10px] text-slate-400 font-medium">tuntas</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-2">
                    Skor dan feedback tersimpan
                </p>
            </div>

            <!-- Metric 5: Pengumpulan Terlambat -->
            <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 shadow-xs hover:border-rose-500/30 transition group col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Lewat Tenggat</span>
                    <div class="w-8 h-8 rounded-xl {{ $lateCount > 0 ? 'bg-amber-500/10 text-amber-600' : 'bg-emerald-500/10 text-emerald-600' }} flex items-center justify-center text-base shrink-0 group-hover:scale-105 transition-transform">
                        <i class="ti ti-clock-exclamation"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-xl sm:text-2xl font-extrabold {{ $lateCount > 0 ? 'text-amber-600' : 'text-emerald-600' }} tracking-tight">{{ $lateCount }}</h3>
                    <span class="text-[10px] text-slate-400 font-medium">berkas</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-2">
                    {{ $lateCount > 0 ? 'Diserahkan setelah deadline' : 'Semua tepat waktu' }}
                </p>
            </div>
        </div>

        <!-- Collapsible Brief / Instruksi Tugas & Lampiran File -->
        <div class="bg-white/85 dark:bg-slate-900/85 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs" x-data="{ expanded: false }">
            <div class="flex items-center justify-between gap-3 cursor-pointer" @click="expanded = !expanded">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base shrink-0">
                        <i class="ti ti-file-text"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Instruksi Tugas & Dokumen Panduan</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Klik untuk melihat detail materi atau berkas yang dilampirkan mentor</p>
                    </div>
                </div>
                <button type="button" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="ti ti-chevron-down text-base transition-transform duration-200" :class="{ 'rotate-180': expanded }"></i>
                </button>
            </div>

            <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3" x-cloak>
                <div class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed bg-slate-50 dark:bg-slate-950/60 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800">
                    {{ $assignment->description }}
                </div>

                @if($assignment->file_attachment)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-500/20">
                        <div class="flex items-center gap-2.5">
                            <i class="ti ti-paperclip text-indigo-600 dark:text-indigo-400 text-base"></i>
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Berkas Lampiran Panduan Tugas</span>
                        </div>
                        <a href="{{ Storage::url($assignment->file_attachment) }}" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-xs transition">
                            <i class="ti ti-download text-xs"></i>
                            <span>Unduh Dokumen</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Filter Controls & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- Segmented Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
                <button type="button" @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-indigo-500/25' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="shrink-0 inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl transition shadow-xs">
                    <i class="ti ti-inbox"></i>
                    <span>Semua Jawaban</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full" :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'">{{ $submittedCount }}</span>
                </button>

                <button type="button" @click="activeTab = 'unreviewed'" :class="activeTab === 'unreviewed' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-indigo-500/25' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="shrink-0 inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl transition shadow-xs">
                    <i class="ti ti-clock-pause"></i>
                    <span>Perlu Dinilai</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full" :class="activeTab === 'unreviewed' ? 'bg-white/20 text-white' : 'bg-amber-500/20 text-amber-700 dark:text-amber-400'">{{ $ungradedCount }}</span>
                </button>

                <button type="button" @click="activeTab = 'reviewed'" :class="activeTab === 'reviewed' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-indigo-500/25' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="shrink-0 inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl transition shadow-xs">
                    <i class="ti ti-award"></i>
                    <span>Sudah Dinilai</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full" :class="activeTab === 'reviewed' ? 'bg-white/20 text-white' : 'bg-purple-500/20 text-purple-700 dark:text-purple-400'">{{ $gradedCount }}</span>
                </button>

                <button type="button" @click="activeTab = 'unsubmitted'" :class="activeTab === 'unsubmitted' ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-indigo-500/25' : 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="shrink-0 inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl transition shadow-xs">
                    <i class="ti ti-users-minus"></i>
                    <span>Belum Kumpul</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-extrabold rounded-full" :class="activeTab === 'unsubmitted' ? 'bg-white/20 text-white' : 'bg-rose-500/20 text-rose-700 dark:text-rose-400'">{{ $unsubmittedUsers->count() }}</span>
                </button>
            </div>

            <!-- Instant Search Input -->
            <div class="relative w-full sm:w-64">
                <i class="ti ti-search absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari nama atau email..." class="w-full bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl pl-9 pr-3 py-2 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 shadow-xs transition">
            </div>
        </div>

        <!-- SECTION 1: DAFTAR SUBMISSIONS (JIKA TAB != 'unsubmitted') -->
        <div x-show="activeTab !== 'unsubmitted'" class="space-y-3.5">
            @forelse($assignment->submissions->sortByDesc('submitted_at') as $sub)
                @php
                    $user = $sub->user;
                    $isLate = $sub->isLate();
                    $fileExt = strtolower(pathinfo($sub->submission_file ?? '', PATHINFO_EXTENSION));
                    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                    $fileUrl = $sub->submission_file ? Storage::url($sub->submission_file) : null;
                @endphp
                <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs hover:border-indigo-500/40 transition space-y-4"
                     x-show="(activeTab === 'all' || 
                             (activeTab === 'unreviewed' && {{ $sub->isGraded() ? 'false' : 'true' }}) || 
                             (activeTab === 'reviewed' && {{ $sub->isGraded() ? 'true' : 'false' }})) && 
                             (searchQuery === '' || '{{ strtolower(addslashes($user->name ?? '')) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($user->email ?? '')) }}'.includes(searchQuery.toLowerCase()))">
                    
                    <!-- Row 1: Student Meta, Timing Badge & Grade Status -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800/80 pb-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm border border-indigo-500/20 shrink-0">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $user->name ?? 'Karyawan' }}</h4>
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        {{ $user->role ?? 'Anggota' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    <span>{{ $user->email ?? '-' }}</span> &bull; 
                                    <span class="font-mono text-slate-600 dark:text-slate-300"><i class="ti ti-clock"></i> {{ $sub->submitted_at->format('d M Y, H:i') }} WIB</span>
                                </p>
                            </div>
                        </div>

                        <!-- Timing & Grade Badges -->
                        <div class="flex items-center gap-2 shrink-0">
                            @if($isLate)
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 flex items-center gap-1">
                                    <i class="ti ti-clock-exclamation"></i> Terlambat
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                                    <i class="ti ti-check"></i> Tepat Waktu
                                </span>
                            @endif

                            @if($sub->isGraded())
                                <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-lg bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20 flex items-center gap-1">
                                    <i class="ti ti-award"></i> Nilai: {{ $sub->score }}/100
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20 flex items-center gap-1">
                                    <i class="ti ti-clock"></i> Belum Dinilai
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Row 2: Submission Content (File / Photo / Link / Notes) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <!-- Left: File Upload or Photo Preview -->
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Berkas / Tautan Tugas</span>
                            
                            @if($sub->submission_file)
                                @if($isImage)
                                    <!-- Photo Thumbnail with Modal Click -->
                                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800">
                                        <button type="button" @click="previewImageSrc = '{{ $fileUrl }}'; previewImageTitle = 'Lampiran Foto Tugas - {{ addslashes($user->name ?? 'Karyawan') }}';" class="group relative shrink-0 overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
                                            <img src="{{ $fileUrl }}" alt="Bukti Tugas" class="w-16 h-16 object-cover group-hover:scale-105 transition-transform duration-200">
                                            <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition-opacity">
                                                <i class="ti ti-zoom-in text-base"></i>
                                            </div>
                                        </button>
                                        <div class="min-w-0 flex-1 space-y-1">
                                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900 dark:text-white truncate">
                                                <i class="ti ti-photo text-indigo-500"></i>
                                                <span class="truncate">{{ basename($sub->submission_file) }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-400">Lampiran format gambar ({{ strtoupper($fileExt) }})</p>
                                            <div class="flex items-center gap-2 pt-0.5">
                                                <button type="button" @click="previewImageSrc = '{{ $fileUrl }}'; previewImageTitle = 'Lampiran Foto Tugas - {{ addslashes($user->name ?? 'Karyawan') }}';" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    Lihat Foto
                                                </button>
                                                <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                                                <a href="{{ $fileUrl }}" target="_blank" class="text-[11px] font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
                                                    Tab Baru &rarr;
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Document / Archive File -->
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-9 h-9 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base shrink-0">
                                                <i class="ti ti-file"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ basename($sub->submission_file) }}</p>
                                                <p class="text-[10px] text-slate-400 uppercase font-mono">{{ $fileExt }} Dokumen</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <a href="{{ $fileUrl }}" target="_blank" title="Pratinjau Berkas" class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-indigo-600 text-xs font-semibold transition">
                                                <i class="ti ti-external-link"></i>
                                            </a>
                                            <a href="{{ $fileUrl }}" download title="Unduh Berkas" class="px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-xs transition flex items-center gap-1">
                                                <i class="ti ti-download"></i>
                                                <span class="hidden sm:inline">Unduh</span>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if($sub->submission_url)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-500/20">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <i class="ti ti-link text-indigo-600 dark:text-indigo-400 text-base shrink-0"></i>
                                        <a href="{{ $sub->submission_url }}" target="_blank" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline truncate">
                                            {{ $sub->submission_url }}
                                        </a>
                                    </div>
                                    <a href="{{ $sub->submission_url }}" target="_blank" class="p-1 text-slate-400 hover:text-indigo-600 shrink-0">
                                        <i class="ti ti-external-link text-sm"></i>
                                    </a>
                                </div>
                            @endif

                            @if(!$sub->submission_file && !$sub->submission_url)
                                <p class="text-xs text-slate-400 italic">Tidak ada berkas atau tautan yang dilampirkan.</p>
                            @endif
                        </div>

                        <!-- Right: Notes & Mentor Feedback Preview -->
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Catatan & Ulasan</span>

                            @if($sub->notes)
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 space-y-1">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1">
                                        <i class="ti ti-message-dots"></i> Catatan Siswa:
                                    </p>
                                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed italic">
                                        "{{ $sub->notes }}"
                                    </p>
                                </div>
                            @endif

                            @if($sub->isGraded() && $sub->feedback)
                                <div class="p-3 rounded-xl bg-purple-50/60 dark:bg-purple-950/20 border border-purple-500/20 space-y-1">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 flex items-center gap-1">
                                        <i class="ti ti-award"></i> Umpan Balik Mentor:
                                    </p>
                                    <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed italic">
                                        "{{ $sub->feedback }}"
                                    </p>
                                </div>
                            @endif

                            @if(!$sub->notes && !($sub->isGraded() && $sub->feedback))
                                <p class="text-xs text-slate-400 italic">Belum ada catatan atau umpan balik tertulis.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Row 3: Action Button to Grade / Re-grade -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center flex-wrap gap-2 text-xs">
                            <span class="text-slate-400">Status:</span>
                            @if($sub->isGraded())
                                @if($sub->isRevisionNeeded())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                        <i class="ti ti-refresh"></i> Perlu Revisi (Skor {{ $sub->score }}/100)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        <i class="ti ti-circle-check"></i> Telah Dinilai (Skor {{ $sub->score }}/100)
                                    </span>
                                @endif
                                @if($sub->grader)
                                    <span class="text-[11px] text-slate-400">
                                        oleh <strong class="text-slate-700 dark:text-slate-300">{{ $sub->grader->name }}</strong>
                                        @if($sub->graded_at)
                                            &bull; {{ $sub->graded_at->translatedFormat('d M Y H:i') }}
                                        @endif
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    <i class="ti ti-clock"></i> Belum Dinilai
                                </span>
                            @endif
                        </div>

                        <button type="button" @click="
                            currentSubId = {{ $sub->id }};
                            currentUserName = '{{ addslashes($user->name ?? 'Karyawan') }}';
                            currentScore = '{{ old('score', $sub->score ?? 85) }}';
                            currentFeedback = '{{ addslashes($sub->feedback ?? '') }}';
                            currentStatus = '{{ $sub->status ?? ($sub->isGraded() ? 'graded' : 'graded') }}';
                            openGradeModal = true;
                        " class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl transition active:scale-95 shadow-xs {{ $sub->isGraded() ? 'bg-purple-500/10 hover:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-500/20' : 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white shadow-indigo-500/25' }}">
                            <i class="ti {{ $sub->isGraded() ? 'ti-edit' : 'ti-award' }}"></i>
                            <span>{{ $sub->isGraded() ? 'Ubah Nilai / Masukan' : 'Beri Nilai Sekarang' }}</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-10 text-center space-y-3 shadow-xs">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                        <i class="ti ti-inbox"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Pengumpulan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                        Belum ada anggota atau karyawan yang menyerahkan jawaban untuk tugas ini.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- SECTION 2: DAFTAR ANGGOTA BELUM MENGUMPULKAN (JIKA TAB == 'unsubmitted') -->
        <div x-show="activeTab === 'unsubmitted'" class="space-y-3.5" x-cloak>
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="ti ti-users-minus text-rose-500"></i>
                            <span>Daftar Anggota Belum Mengumpulkan ({{ $unsubmittedUsers->count() }})</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Berikut adalah karyawan/siswa aktif yang ditargetkan namun belum mengunggah tugas. Anda dapat memberikan perpanjangan tenggat khusus per-individu.</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($unsubmittedUsers as $unsub)
                        @php
                            $override = $userOverrides->get($unsub->id);
                            $hasActiveOverride = $override && $override->is_unlocked && $override->custom_due_date;
                        @endphp
                        <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3" x-show="searchQuery === '' || '{{ strtolower(addslashes($unsub->name)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($unsub->email)) }}'.includes(searchQuery.toLowerCase())">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs border border-slate-200 dark:border-slate-700 shrink-0">
                                    {{ strtoupper(substr($unsub->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-xs text-slate-900 dark:text-white">{{ $unsub->name }}</h4>
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                            {{ $unsub->role }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400">{{ $unsub->email }}</p>
                                    @if($hasActiveOverride)
                                        <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded-md border border-indigo-200 dark:border-indigo-800">
                                                <i class="ti ti-calendar-time"></i>
                                                Tenggat Khusus: {{ $override->custom_due_date->translatedFormat('d M Y, H:i') }} WIB
                                            </span>
                                            @if($override->reason)
                                                <span class="text-[10px] text-slate-400 italic">"{{ $override->reason }}"</span>
                                            @endif
                                            @if($override->unlockedBy)
                                                <span class="text-[10px] text-slate-400">&bull; oleh {{ $override->unlockedBy->name }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $hasActiveOverride ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }} flex items-center gap-1">
                                    <i class="ti {{ $hasActiveOverride ? 'ti-calendar-check' : 'ti-clock' }}"></i>
                                    {{ $hasActiveOverride ? 'Waktu Diperpanjang' : 'Belum Kumpul' }}
                                </span>

                                <button type="button" 
                                        @click="
                                            extendUserId = {{ $unsub->id }};
                                            extendUserName = '{{ addslashes($unsub->name) }}';
                                            extendCustomDueDate = '{{ $hasActiveOverride ? $override->custom_due_date->format('Y-m-d\TH:i') : now()->addDays(2)->format('Y-m-d\TH:i') }}';
                                            extendReason = '{{ addslashes($override->reason ?? '') }}';
                                            openExtendModal = true;
                                        " 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-600/15 dark:hover:bg-indigo-600/30 text-indigo-700 dark:text-indigo-300 font-semibold text-xs rounded-xl border border-indigo-200 dark:border-indigo-500/30 transition shadow-xs active:scale-95">
                                    <i class="ti ti-calendar-plus text-sm"></i>
                                    <span>{{ $hasActiveOverride ? 'Ubah Tenggat Khusus' : 'Buka Kembali / Beri Waktu Tambahan' }}</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto text-2xl">
                                <i class="ti ti-circle-check"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Luar Biasa!</h4>
                            <p class="text-xs text-slate-500">Seluruh anggota tim yang ditargetkan sudah mengumpulkan tugas ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ==================== MODAL PENILAIAN TUGAS (MENTOR GRADE MODAL) ==================== -->
        <div x-show="openGradeModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="openGradeModal = false" 
                 x-show="openGradeModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 max-w-md w-full text-left space-y-4 shadow-2xl">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                            <i class="ti ti-award"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Form Penilaian & Evaluasi Tugas</h3>
                            <p class="text-[11px] text-slate-500">Pengumpul: <strong class="text-slate-800 dark:text-slate-200" x-text="currentUserName"></strong></p>
                        </div>
                    </div>
                    <button type="button" @click="openGradeModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>

                <form method="POST" :action="'/submissions/' + currentSubId + '/grade'" @submit.prevent="submitGrade($event)" class="space-y-4">
                    @csrf

                    <div x-show="gradeError" class="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl text-xs flex items-center gap-2" x-cloak>
                        <i class="ti ti-alert-circle text-base shrink-0"></i>
                        <span x-text="gradeError"></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Nilai / Skor Tugas (0 - 100) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="score" min="0" max="100" x-model="currentScore" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-lg font-extrabold rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">

                        <!-- Rapid Preset Buttons -->
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="text-[10px] text-slate-400 font-medium">Preset:</span>
                            <button type="button" @click="currentScore = 70" class="px-2 py-0.5 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600">70</button>
                            <button type="button" @click="currentScore = 80" class="px-2 py-0.5 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600">80</button>
                            <button type="button" @click="currentScore = 85" class="px-2 py-0.5 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600">85</button>
                            <button type="button" @click="currentScore = 90" class="px-2 py-0.5 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600">90</button>
                            <button type="button" @click="currentScore = 95" class="px-2 py-0.5 text-[11px] font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-50 hover:text-indigo-600">95</button>
                            <button type="button" @click="currentScore = 100" class="px-2 py-0.5 text-[11px] font-bold rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-500/20">100</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Status Penilaian
                        </label>
                        <select name="status" x-model="currentStatus" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                            <option value="graded">Telah Dinilai (Selesai)</option>
                            <option value="revision_needed">Perlu Revisi (Memerlukan Perbaikan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Catatan Masukan / Evaluasi Mentor
                        </label>
                        <textarea name="feedback" rows="3" x-model="currentFeedback" placeholder="Tuliskan catatan apresiasi, revisi atau masukan perbaikan..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="openGradeModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSavingGrade" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white text-xs font-semibold rounded-xl shadow-xs transition active:scale-95 flex items-center gap-1.5 disabled:opacity-50">
                            <span x-show="!isSavingGrade" class="flex items-center gap-1.5"><i class="ti ti-device-floppy"></i> <span>Simpan Nilai</span></span>
                            <span x-show="isSavingGrade" class="flex items-center gap-1.5" x-cloak><i class="ti ti-loader animate-spin"></i> <span>Menyimpan...</span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ==================== MODAL PERPANJANGAN TUGAS PER-INDIVIDU ==================== -->
        <div x-show="openExtendModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="openExtendModal = false" 
                 x-show="openExtendModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 max-w-md w-full text-left space-y-4 shadow-2xl">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                            <i class="ti ti-calendar-plus"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900 dark:text-white">Buka Kembali / Perpanjang Tenggat</h3>
                            <p class="text-[11px] text-slate-500">Khusus Anggota: <strong class="text-indigo-600 dark:text-indigo-400" x-text="extendUserName"></strong></p>
                        </div>
                    </div>
                    <button type="button" @click="openExtendModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('assignments.extend-user', $assignment->id) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="user_id" :value="extendUserId">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Batas Waktu Baru Khusus (Custom Due Date) <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="custom_due_date" 
                               x-model="extendCustomDueDate" 
                               required 
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs font-medium rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        <p class="text-[10px] text-slate-400 mt-1">Hanya anggota ini yang dapat mengumpulkan tugas hingga waktu baru tersebut.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Alasan / Catatan Perpanjangan (Opsional)
                        </label>
                        <textarea name="reason" 
                                  rows="3" 
                                  x-model="extendReason" 
                                  placeholder="Contoh: Izin sakit, dispensasi khusus perbaikan sistem..." 
                                  class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="openExtendModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white text-xs font-semibold rounded-xl shadow-xs transition active:scale-95 flex items-center gap-1.5">
                            <i class="ti ti-device-floppy"></i>
                            <span>Simpan Perpanjangan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ==================== MODAL PREVIEW FOTO / GAMBAR ==================== -->
        <div x-show="previewImageSrc" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4" x-cloak>
            <div @click.away="previewImageSrc = null" 
                 x-show="previewImageSrc"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl">
                
                <div class="flex items-center justify-between p-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="previewImageTitle"></h3>
                    <div class="flex items-center gap-2">
                        <a :href="previewImageSrc" target="_blank" class="p-1.5 text-xs text-indigo-600 hover:underline font-semibold flex items-center gap-1">
                            <i class="ti ti-external-link"></i> Tab Baru
                        </a>
                        <button type="button" @click="previewImageSrc = null" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white">
                            <i class="ti ti-x text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="p-4 bg-slate-950 flex items-center justify-center max-h-[75vh] overflow-auto">
                    <img :src="previewImageSrc" alt="Preview" class="max-w-full max-h-[70vh] object-contain rounded-lg">
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
