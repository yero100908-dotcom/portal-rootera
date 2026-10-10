<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 w-full">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <a href="{{ route('assignments.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white bg-slate-100 dark:bg-slate-800/80 transition group">
                        <i class="ti ti-arrow-left group-hover:-translate-x-0.5 transition-transform"></i>
                        <span>Kembali ke Tugas</span>
                    </a>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white tracking-tight truncate flex items-center gap-2">
                    <span class="truncate">{{ $assignment->title }}</span>
                </h1>
            </div>
            @if($isManager)
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('assignments.edit', $assignment->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-xl border border-slate-200/80 dark:border-slate-700 transition">
                        <i class="ti ti-edit text-sm"></i>
                        <span>Edit Tugas</span>
                    </a>

                    <form method="POST" action="{{ route('assignments.destroy', $assignment->id) }}" onsubmit="return confirm('Hapus tugas ini beserta seluruh pengumpulannya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-xl border border-rose-500/20 transition flex items-center justify-center">
                            <i class="ti ti-trash text-sm"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-5 sm:space-y-6" x-data="{ activeTab: 'all', openGradeModal: null }">
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

        <!-- Assignment Information Card -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 sm:p-6 shadow-xs space-y-5">
            <!-- Header Row -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800/80 pb-5">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl font-bold shrink-0 shadow-xs">
                        <i class="ti ti-file-text"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
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

                            <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                <i class="ti ti-user-circle text-sm text-indigo-500"></i>
                                <span>Dibuat oleh: <strong class="text-slate-700 dark:text-slate-200 font-semibold">{{ $assignment->creator->name ?? 'Admin' }}</strong></span>
                            </span>
                        </div>
                        <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white leading-snug">
                            {{ $assignment->title }}
                        </h2>
                    </div>
                </div>

                <!-- Deadline Badge -->
                <div class="bg-slate-50 dark:bg-slate-950/80 px-4 py-3 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 flex items-center gap-3 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shrink-0">
                        <i class="ti ti-calendar-time"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tenggat Waktu (Deadline)</p>
                        <p class="text-xs sm:text-sm font-bold font-mono {{ $assignment->isOverdue() ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">
                            {{ $assignment->deadline->format('d M Y, H:i') }} WIB
                        </p>
                        <p class="text-[10px] font-medium {{ $assignment->isOverdue() ? 'text-rose-500' : 'text-emerald-500' }}">
                            {{ $assignment->isOverdue() ? 'Lewat ' . $assignment->deadline->diffForHumans() : 'Sisa ' . $assignment->deadline->diffForHumans(null, true) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Description / Instructions -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <i class="ti ti-notes text-indigo-500"></i>
                    <span>Instruksi & Petunjuk Pengerjaan:</span>
                </h3>
                <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line bg-slate-50 dark:bg-slate-950/60 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 font-sans">
                    {{ $assignment->description }}
                </div>
            </div>

            <!-- Attachment File Download Card -->
            @if($assignment->file_attachment)
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-indigo-50/50 dark:bg-indigo-950/20 p-3.5 sm:p-4 rounded-2xl border border-indigo-500/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg shrink-0 shadow-xs shadow-indigo-500/25">
                            <i class="ti ti-paperclip"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white">File Lampiran / Panduan Tugas</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Unduh dokumen instruksi atau materi pendukung yang dilampirkan mentor</p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($assignment->file_attachment) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-xs transition active:scale-95 shrink-0">
                        <i class="ti ti-download"></i>
                        <span>Unduh Lampiran</span>
                    </a>
                </div>
            @endif
        </div>

        @if($isManager)
            <!-- MANAGER VIEW: Submissions Matrix & Mobile Cards -->
            <div class="space-y-4">
                <!-- Header & Segmented Tab Filters -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <i class="ti ti-users-group text-indigo-600 dark:text-indigo-400"></i>
                            <span>Rekapitulasi Pengumpulan Karyawan</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Daftar tim/karyawan, status pengumpulan, berkas jawaban, dan formulir penilaian
                        </p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-900/90 p-1 rounded-xl border border-slate-200/80 dark:border-slate-800 text-xs shrink-0 self-start sm:self-auto">
                        <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'" class="px-3 py-1.5 rounded-lg transition">
                            Semua ({{ $allUsers->count() }})
                        </button>
                        <button @click="activeTab = 'submitted'" :class="activeTab === 'submitted' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'" class="px-3 py-1.5 rounded-lg transition">
                            Sudah Kumpul ({{ $assignment->submissions->count() }})
                        </button>
                        <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'" class="px-3 py-1.5 rounded-lg transition">
                            Belum ({{ $allUsers->count() - $assignment->submissions->count() }})
                        </button>
                    </div>
                </div>

                <!-- 1. DESKTOP VIEW: Clean Modern Table (hidden on mobile, shown on md+) -->
                <div class="hidden md:block bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-950 text-[11px] uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                                <tr>
                                    <th class="px-4 py-3.5">Nama Karyawan</th>
                                    <th class="px-4 py-3.5">Status Pengumpulan</th>
                                    <th class="px-4 py-3.5">File / Link Repositori</th>
                                    <th class="px-4 py-3.5">Catatan</th>
                                    <th class="px-4 py-3.5">Nilai & Feedback</th>
                                    <th class="px-4 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @foreach($allUsers as $u)
                                    @php
                                        $sub = $assignment->submissions->firstWhere('user_id', $u->id);
                                        $isSubmitted = !is_null($sub);
                                    @endphp
                                    <tr x-show="activeTab === 'all' || (activeTab === 'submitted' && {{ $isSubmitted ? 'true' : 'false' }}) || (activeTab === 'pending' && {{ !$isSubmitted ? 'true' : 'false' }})" class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition">
                                        <!-- User Info -->
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-xs shrink-0 border border-indigo-500/20">
                                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 dark:text-white">{{ $u->name }}</div>
                                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ $u->role }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-4 py-3.5">
                                            @if($isSubmitted)
                                                <div class="space-y-1">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-bold rounded-lg border
                                                        @if($sub->isLate()) bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                                        @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                                                        <i class="ti ti-circle-check text-xs"></i>
                                                        <span>{{ $sub->isLate() ? 'Terlambat' : 'Tepat Waktu' }}</span>
                                                    </span>
                                                    <div class="text-[10px] text-slate-400 font-mono">{{ $sub->submitted_at->format('d M H:i') }}</div>
                                                </div>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-medium text-slate-400 border border-slate-200 dark:border-slate-800 rounded-lg">
                                                    Belum Mengumpulkan
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Submission File / URL -->
                                        <td class="px-4 py-3.5">
                                            @if($isSubmitted)
                                                <div class="flex flex-col gap-1.5">
                                                    @if($sub->submission_file)
                                                        <a href="{{ Storage::url($sub->submission_file) }}" target="_blank" class="inline-flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-semibold">
                                                            <i class="ti ti-download text-sm"></i>
                                                            <span>Unduh File</span>
                                                        </a>
                                                    @endif
                                                    @if($sub->submission_url)
                                                        <a href="{{ $sub->submission_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:underline text-xs font-semibold">
                                                            <i class="ti ti-external-link text-sm"></i>
                                                            <span>Link Repositori</span>
                                                        </a>
                                                    @endif
                                                    @if(!$sub->submission_file && !$sub->submission_url)
                                                        <span class="text-slate-400 text-[11px]">-</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Notes -->
                                        <td class="px-4 py-3.5 max-w-xs text-slate-600 dark:text-slate-300">
                                            @if($isSubmitted && $sub->notes)
                                                <span class="truncate block text-xs" title="{{ $sub->notes }}">{{ $sub->notes }}</span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Grade & Feedback -->
                                        <td class="px-4 py-3.5">
                                            @if($isSubmitted && $sub->isGraded())
                                                <div>
                                                    <span class="px-2.5 py-1 text-xs font-extrabold bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20 rounded-lg">
                                                        Nilai: {{ $sub->score }}/100
                                                    </span>
                                                    @if($sub->feedback)
                                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 italic truncate max-w-xs" title="{{ $sub->feedback }}">
                                                            "{{ $sub->feedback }}"
                                                        </p>
                                                    @endif
                                                </div>
                                            @elseif($isSubmitted)
                                                <span class="text-amber-600 dark:text-amber-400 text-[11px] font-semibold flex items-center gap-1">
                                                    <i class="ti ti-clock"></i> Belum Dinilai
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Action button -->
                                        <td class="px-4 py-3.5 text-right">
                                            @if($isSubmitted)
                                                <button @click="openGradeModal = {{ $sub->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-xs transition active:scale-95 ml-auto">
                                                    <i class="ti ti-award"></i>
                                                    <span>{{ $sub->isGraded() ? 'Edit Nilai' : 'Beri Nilai' }}</span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. MOBILE VIEW: Responsive Card Stack (shown on mobile, hidden on md+) -->
                <div class="block md:hidden space-y-3.5">
                    @foreach($allUsers as $u)
                        @php
                            $sub = $assignment->submissions->firstWhere('user_id', $u->id);
                            $isSubmitted = !is_null($sub);
                        @endphp
                        <div x-show="activeTab === 'all' || (activeTab === 'submitted' && {{ $isSubmitted ? 'true' : 'false' }}) || (activeTab === 'pending' && {{ !$isSubmitted ? 'true' : 'false' }})" class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-4 shadow-xs space-y-3">
                            <!-- User Top Row -->
                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/80 pb-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-xs shrink-0 border border-indigo-500/20">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $u->name }}</h4>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ $u->role }}</p>
                                    </div>
                                </div>
                                <!-- Status Badge -->
                                @if($isSubmitted)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-bold rounded-lg border shrink-0
                                        @if($sub->isLate()) bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                        @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                                        <i class="ti ti-circle-check text-xs"></i>
                                        <span>{{ $sub->isLate() ? 'Terlambat' : 'Terkumpul' }}</span>
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-medium text-slate-400 border border-slate-200 dark:border-slate-800 rounded-lg shrink-0">
                                        Belum Kumpul
                                    </span>
                                @endif
                            </div>

                            @if($isSubmitted)
                                <!-- Submission details on mobile -->
                                <div class="space-y-2 text-xs">
                                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                                        <span>Waktu Submit:</span>
                                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ $sub->submitted_at->format('d M Y, H:i') }} WIB</span>
                                    </div>

                                    <!-- Files & Links -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        @if($sub->submission_file)
                                            <a href="{{ Storage::url($sub->submission_file) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 text-xs font-semibold border border-indigo-500/20">
                                                <i class="ti ti-download"></i> Unduh File
                                            </a>
                                        @endif
                                        @if($sub->submission_url)
                                            <a href="{{ $sub->submission_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-semibold border border-blue-500/20">
                                                <i class="ti ti-external-link"></i> Link Repositori
                                            </a>
                                        @endif
                                    </div>

                                    @if($sub->notes)
                                        <div class="bg-slate-50 dark:bg-slate-950/60 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-800/60 text-slate-600 dark:text-slate-400 text-[11px]">
                                            <strong class="text-slate-700 dark:text-slate-300 block mb-0.5">Catatan Karyawan:</strong>
                                            {{ $sub->notes }}
                                        </div>
                                    @endif

                                    <!-- Grade & Feedback Box -->
                                    @if($sub->isGraded())
                                        <div class="p-3 bg-purple-500/10 border border-purple-500/20 rounded-xl space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider">Nilai Final</span>
                                                <span class="text-base font-extrabold text-purple-700 dark:text-purple-300 font-mono">{{ $sub->score }}/100</span>
                                            </div>
                                            @if($sub->feedback)
                                                <p class="text-[11px] text-slate-600 dark:text-slate-300 italic pt-1 border-t border-purple-500/20">
                                                    "{{ $sub->feedback }}"
                                                </p>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Mobile Grade Button -->
                                    <div class="pt-2">
                                        <button @click="openGradeModal = {{ $sub->id }}" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 active:scale-95">
                                            <i class="ti ti-award text-sm"></i>
                                            <span>{{ $sub->isGraded() ? 'Edit Penilaian' : 'Beri Nilai Sekarang' }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Modals for Grading (Accessible for both desktop & mobile) -->
                @foreach($assignment->submissions as $sub)
                    <div x-show="openGradeModal === {{ $sub->id }}" x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md">
                        
                        <div @click.away="openGradeModal = null" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 max-w-md w-full text-left space-y-4 shadow-2xl">
                            
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">
                                        <i class="ti ti-award"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">Form Penilaian Tugas</h3>
                                        <p class="text-[11px] text-slate-500">{{ $sub->user->name ?? 'Karyawan' }}</p>
                                    </div>
                                </div>
                                <button @click="openGradeModal = null" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                    <i class="ti ti-x text-lg"></i>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('submissions.grade', $sub->id) }}" class="space-y-4" x-data="{ currentScore: '{{ old('score', $sub->score ?? 85) }}' }">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                        Nilai / Score (0 - 100) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" name="score" min="0" max="100" x-model="currentScore" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-base font-extrabold rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">

                                    <!-- Quick Preset Buttons for Rapid Mobile Grading -->
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
                                        Umpan Balik / Feedback Mentor
                                    </label>
                                    <textarea name="feedback" rows="3" placeholder="Tuliskan catatan apresiasi, saran perbaikan, atau poin evaluasi..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('feedback', $sub->feedback) }}</textarea>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" @click="openGradeModal = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-xs transition active:scale-95">
                                        Simpan Nilai
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- KARYAWAN VIEW: Submission Status & Upload Form (Responsive 1-col mobile, 3-col desktop) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
                <!-- Status & Feedback Card (1 Column) -->
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="ti ti-dashboard text-indigo-500"></i>
                            <span>Status Pengumpulan Anda</span>
                        </h3>

                        @if($mySubmission)
                            <div class="p-4 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                                        <i class="ti ti-circle-check text-base"></i>
                                        <span>Sudah Dikumpulkan</span>
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400">
                                        {{ $mySubmission->submitted_at->format('d M, H:i') }}
                                    </span>
                                </div>

                                @if($mySubmission->isLate())
                                    <p class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                        <i class="ti ti-clock-exclamation"></i>
                                        <span>Dikumpulkan melebihi batas waktu deadline</span>
                                    </p>
                                @else
                                    <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                        <i class="ti ti-check"></i>
                                        <span>Dikumpulkan tepat waktu</span>
                                    </p>
                                @endif
                            </div>

                            <!-- Grade & Feedback Section if graded -->
                            @if($mySubmission->isGraded())
                                <div class="p-4 sm:p-5 bg-gradient-to-br from-purple-500/10 via-indigo-500/5 to-transparent border border-purple-500/25 rounded-2xl space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 flex items-center gap-1">
                                            <i class="ti ti-trophy text-sm"></i>
                                            <span>Hasil Penilaian Mentor</span>
                                        </p>
                                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Tuntas</span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-3xl sm:text-4xl font-extrabold text-purple-700 dark:text-purple-300 font-mono">
                                            {{ $mySubmission->score }}
                                        </span>
                                        <span class="text-xs font-semibold text-purple-500">/ 100</span>
                                    </div>
                                    @if($mySubmission->feedback)
                                        <div class="pt-2 border-t border-purple-500/20 text-xs text-slate-700 dark:text-slate-300 italic leading-relaxed">
                                            "{{ $mySubmission->feedback }}"
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="p-3.5 bg-amber-500/10 border border-amber-500/20 rounded-2xl text-xs text-amber-700 dark:text-amber-400 flex items-center gap-2">
                                    <i class="ti ti-clock text-base shrink-0"></i>
                                    <span>Tugas Anda sedang menunggu review & penilaian dari mentor.</span>
                                </div>
                            @endif
                        @if($userOverride && $userOverride->is_unlocked && $userOverride->custom_due_date)
                            <div class="p-4 bg-indigo-500/10 border border-indigo-500/25 rounded-2xl space-y-1">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                    <i class="ti ti-calendar-time text-base"></i>
                                    <span>Tenggat Waktu Khusus Anda</span>
                                </div>
                                <p class="text-xs font-mono font-bold text-indigo-700 dark:text-indigo-300">
                                    {{ $userOverride->custom_due_date->translatedFormat('d F Y, H:i') }} WIB
                                </p>
                                @if($userOverride->reason)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 italic">
                                        Catatan Mentor: "{{ $userOverride->reason }}"
                                    </p>
                                @endif
                            </div>
                        @endif

                        @else
                            <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl space-y-1.5">
                                <span class="text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                                    <i class="ti ti-alert-triangle text-base"></i>
                                    <span>Belum Dikumpulkan</span>
                                </span>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Kumpulkan berkas file atau link tautan repositori tugas Anda sebelum waktu deadline berakhir.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Upload Submission (2 Columns) -->
                <div class="lg:col-span-2">
                    <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 sm:p-6 shadow-xs space-y-5" x-data="{ selectedFileName: '', selectedFileSize: '' }">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-3.5">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="ti ti-upload text-indigo-600 dark:text-indigo-400"></i>
                                <span>{{ $mySubmission ? 'Perbarui Pengumpulan Tugas' : 'Form Pengumpulan Tugas' }}</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Silakan unggah dokumen/arsip atau cantumkan tautan URL proyek GitHub / Google Drive Anda
                            </p>
                        </div>

                        @if(!$isManager && $assignment->isOverdueForUser(Auth::user()))
                            <div class="p-5 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-xs text-rose-700 dark:text-rose-400 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center shrink-0 text-base">
                                    <i class="ti ti-clock-off"></i>
                                </div>
                                <div class="space-y-1">
                                    <strong class="block font-bold text-sm">Waktu Pengumpulan Tugas Telah Berakhir</strong>
                                    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                                        Batas waktu deadline telah terlewati dan pengumpulan telah ditutup. Silakan hubungi Mentor atau Owner jika Anda memerlukan pembukaan kembali atau perpanjangan tenggat waktu secara khusus.
                                    </p>
                                </div>
                            </div>
                        @else
                        <form method="POST" action="{{ route('assignments.submit', $assignment->id) }}" enctype="multipart/form-data" class="space-y-4.5">
                            @csrf

                            <!-- File Upload Area -->
                            <div>
                                <label for="submission_file" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Unggah File Tugas (PDF, DOCX, ZIP, Gambar, Maks 10MB)
                                </label>
                                
                                <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 rounded-2xl p-4 sm:p-5 text-center transition bg-slate-50/50 dark:bg-slate-950/40 group">
                                    <input type="file" id="submission_file" name="submission_file" 
                                           @change="if($event.target.files.length > 0) { selectedFileName = $event.target.files[0].name; selectedFileSize = ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB'; }"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    
                                    <div class="space-y-2 pointer-events-none">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto text-xl group-hover:scale-110 transition-transform">
                                            <i class="ti ti-cloud-upload"></i>
                                        </div>
                                        <div class="text-xs text-slate-600 dark:text-slate-400">
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">Pilih berkas</span> atau seret ke area ini
                                        </div>
                                        <p class="text-[10px] text-slate-400">PDF, DOCX, ZIP, RAR, PNG, JPG (Maks. 10MB)</p>
                                    </div>
                                </div>

                                <!-- Live File Selected Preview -->
                                <template x-if="selectedFileName">
                                    <div class="mt-2.5 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-500/20 flex items-center justify-between text-xs text-indigo-700 dark:text-indigo-300 font-semibold">
                                        <div class="flex items-center gap-2 truncate">
                                            <i class="ti ti-file-check text-base shrink-0"></i>
                                            <span class="truncate" x-text="selectedFileName"></span>
                                            <span class="text-[10px] font-mono text-indigo-500" x-text="'(' + selectedFileSize + ')'"></span>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Siap Dikirim</span>
                                    </div>
                                </template>

                                @if($mySubmission && $mySubmission->submission_file)
                                    <div class="mt-2 text-[11px] text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 font-medium">
                                        <i class="ti ti-file-check"></i>
                                        <span>File terunggah sebelumnya:</span>
                                        <a href="{{ Storage::url($mySubmission->submission_file) }}" target="_blank" class="underline hover:text-indigo-500 font-bold">Lihat / Unduh</a>
                                    </div>
                                @endif

                                @error('submission_file')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submission URL -->
                            <div>
                                <label for="submission_url" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Tautan URL Proyek / Dokumen Eksternal (Opsional)
                                </label>
                                <div class="relative">
                                    <i class="ti ti-link absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                                    <input type="url" id="submission_url" name="submission_url" value="{{ old('submission_url', $mySubmission->submission_url ?? '') }}" placeholder="https://github.com/username/repo atau tautan Google Drive" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl pl-10 pr-3 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                                </div>
                                @error('submission_url')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Catatan Tambahan untuk Mentor (Opsional)
                                </label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Tuliskan catatan singkat mengenai cara menjalankan kode atau rincian hasil pengerjaan..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">{{ old('notes', $mySubmission->notes ?? '') }}</textarea>
                                @error('notes')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-semibold text-xs rounded-xl shadow-sm shadow-indigo-500/25 transition active:scale-95">
                                    <i class="ti ti-send text-sm"></i>
                                    <span>{{ $mySubmission ? 'Perbarui Pengumpulan' : 'Kirim Tugas Sekarang' }}</span>
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
