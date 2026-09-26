<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('assignments.index') }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs font-semibold flex items-center gap-1 transition">
                        <i class="ti ti-arrow-left"></i> Kembali ke Daftar Tugas
                    </a>
                </div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight mt-1 flex items-center gap-2">
                    {{ $assignment->title }}
                </h1>
            </div>
            <div class="flex items-center gap-2">
                @if($isManager)
                    <a href="{{ route('assignments.edit', $assignment->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-400 font-semibold text-xs rounded-lg border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5">
                        <i class="ti ti-edit"></i> Edit Tugas
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: 'all', openGradeModal: null }">
        <!-- Flash Notifications -->
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

        <!-- Assignment Header Card -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 sm:p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800/80 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-bold shrink-0">
                        <i class="ti ti-file-description"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded border
                                @if(!$assignment->is_active) bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20
                                @elseif($assignment->isOverdue()) bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20
                                @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                                @if(!$assignment->is_active) Non-Aktif
                                @elseif($assignment->isOverdue()) Tenggat Lewat
                                @else Aktif @endif
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                Dibuat oleh: <strong class="text-slate-700 dark:text-slate-200">{{ $assignment->creator->name ?? 'Admin' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Deadline Badge -->
                <div class="bg-slate-50 dark:bg-slate-950 px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-800 flex items-center gap-3">
                    <i class="ti ti-clock text-indigo-600 dark:text-indigo-400 text-lg"></i>
                    <div>
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Tenggat Waktu (Deadline)</p>
                        <p class="text-xs font-bold font-mono {{ $assignment->isOverdue() ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">
                            {{ $assignment->deadline->format('d F Y - H:i') }} WIB
                        </p>
                    </div>
                </div>
            </div>

            <!-- Description / Instructions -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Instruksi & Petunjuk Tugas:</h3>
                <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line bg-slate-50 dark:bg-slate-950/50 p-4 rounded-lg border border-slate-200/80 dark:border-slate-800/80">
                    {{ $assignment->description }}
                </div>
            </div>

            <!-- Attachment File -->
            @if($assignment->file_attachment)
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 font-medium">
                        <i class="ti ti-paperclip text-indigo-500 text-sm"></i> File Lampiran Instruksi
                    </span>
                    <a href="{{ Storage::url($assignment->file_attachment) }}" target="_blank" class="px-3 py-1.5 bg-indigo-600/10 hover:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 text-xs font-semibold rounded-lg border border-indigo-500/20 transition flex items-center gap-1.5">
                        <i class="ti ti-download"></i> Unduh Lampiran
                    </a>
                </div>
            @endif
        </div>

        @if($isManager)
            <!-- MANAGER VIEW: Submissions Matrix & Grading -->
            <div class="space-y-4">
                <!-- Header & Tabs -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <i class="ti ti-users text-indigo-600 dark:text-indigo-400"></i> Rekapitulasi Pengumpulan Karyawan
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Daftar karyawan beserta status pengumpulan, file tugas, dan penilaian
                        </p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-900 p-1 rounded-lg border border-slate-200 dark:border-slate-800 text-xs">
                        <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-slate-600 dark:text-slate-400'" class="px-3 py-1 rounded-md transition">
                            Semua ({{ $allUsers->count() }})
                        </button>
                        <button @click="activeTab = 'submitted'" :class="activeTab === 'submitted' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-slate-600 dark:text-slate-400'" class="px-3 py-1 rounded-md transition">
                            Sudah Mengumpulkan ({{ $assignment->submissions->count() }})
                        </button>
                        <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-slate-600 dark:text-slate-400'" class="px-3 py-1 rounded-md transition">
                            Belum ({{ $allUsers->count() - $assignment->submissions->count() }})
                        </button>
                    </div>
                </div>

                <!-- Submissions Table -->
                <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                            <thead class="bg-slate-100 dark:bg-slate-950 text-[11px] uppercase font-semibold text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-4 py-3">Nama Karyawan</th>
                                    <th class="px-4 py-3">Status Pengumpulan</th>
                                    <th class="px-4 py-3">File / Link Repositori</th>
                                    <th class="px-4 py-3">Catatan</th>
                                    <th class="px-4 py-3">Nilai & Feedback</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                                @foreach($allUsers as $u)
                                    @php
                                        $sub = $assignment->submissions->firstWhere('user_id', $u->id);
                                        $isSubmitted = !is_null($sub);
                                    @endphp
                                    <tr x-show="activeTab === 'all' || (activeTab === 'submitted' && {{ $isSubmitted ? 'true' : 'false' }}) || (activeTab === 'pending' && {{ !$isSubmitted ? 'true' : 'false' }})" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                        <!-- User Name & Role -->
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-300 shrink-0">
                                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 dark:text-white">{{ $u->name }}</div>
                                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ $u->role }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-4 py-3">
                                            @if($isSubmitted)
                                                <div class="space-y-1">
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded border inline-flex items-center gap-1
                                                        @if($sub->isLate()) bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                                        @else bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20 @endif">
                                                        <i class="ti ti-circle-check text-xs"></i>
                                                        @if($sub->isLate()) Terlambat @else Tepat Waktu @endif
                                                    </span>
                                                    <div class="text-[10px] text-slate-400 font-mono">{{ $sub->submitted_at->format('d M H:i') }}</div>
                                                </div>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-medium text-slate-400 border border-slate-200 dark:border-slate-800 rounded">
                                                    Belum Mengumpulkan
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Submission File / URL -->
                                        <td class="px-4 py-3">
                                            @if($isSubmitted)
                                                <div class="flex flex-col gap-1">
                                                    @if($sub->submission_file)
                                                        <a href="{{ Storage::url($sub->submission_file) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-semibold">
                                                            <i class="ti ti-download"></i> Unduh File
                                                        </a>
                                                    @endif
                                                    @if($sub->submission_url)
                                                        <a href="{{ $sub->submission_url }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline text-xs font-semibold">
                                                            <i class="ti ti-external-link"></i> Repositori/Dokumen
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Notes -->
                                        <td class="px-4 py-3 max-w-xs text-slate-600 dark:text-slate-300">
                                            @if($isSubmitted && $sub->notes)
                                                <span class="truncate block" title="{{ $sub->notes }}">{{ $sub->notes }}</span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            @endif
                                        </td>

                                        <!-- Grade & Feedback -->
                                        <td class="px-4 py-3">
                                            @if($isSubmitted && $sub->isGraded())
                                                <div>
                                                    <span class="px-2.5 py-0.5 text-xs font-extrabold bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20 rounded">
                                                        Nilai: {{ $sub->score }}/100
                                                    </span>
                                                    @if($sub->feedback)
                                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 italic truncate max-w-xs" title="{{ $sub->feedback }}">
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
                                        <td class="px-4 py-3 text-right">
                                            @if($isSubmitted)
                                                <button @click="openGradeModal = {{ $sub->id }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-semibold rounded-lg shadow-sm transition flex items-center gap-1 ml-auto">
                                                    <i class="ti ti-award"></i> {{ $sub->isGraded() ? 'Edit Nilai' : 'Beri Nilai' }}
                                                </button>

                                                <!-- Inline Modal for Grading -->
                                                <div x-show="openGradeModal === {{ $sub->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
                                                    <div @click.away="openGradeModal = null" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 max-w-md w-full text-left space-y-4 shadow-xl">
                                                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                                                            <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-1.5">
                                                                <i class="ti ti-award text-indigo-500"></i> Penilaian: {{ $u->name }}
                                                            </h3>
                                                            <button @click="openGradeModal = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                                                                <i class="ti ti-x text-base"></i>
                                                            </button>
                                                        </div>

                                                        <form method="POST" action="{{ route('submissions.grade', $sub->id) }}" class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                                                    Nilai / Score (0 - 100) <span class="text-rose-500">*</span>
                                                                </label>
                                                                <input type="number" name="score" min="0" max="100" value="{{ old('score', $sub->score ?? 85) }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-sm font-bold rounded-lg px-3 py-2">
                                                            </div>

                                                            <div>
                                                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                                                                    Feedback / Catatan Revisi
                                                                </label>
                                                                <textarea name="feedback" rows="3" placeholder="Tuliskan catatan apresiasi atau saran perbaikan..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2">{{ old('feedback', $sub->feedback) }}</textarea>
                                                            </div>

                                                            <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                                                                <button type="button" @click="openGradeModal = null" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg">
                                                                    Batal
                                                                </button>
                                                                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm">
                                                                    Simpan Nilai
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @else
            <!-- KARYAWAN VIEW: Submission Form & Status -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Status & Feedback Card -->
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Status Pengumpulan Anda
                        </h3>

                        @if($mySubmission)
                            <div class="p-4 bg-indigo-500/10 border border-indigo-500/20 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                                        <i class="ti ti-circle-check text-base"></i> Sudah Mengumpulkan
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400">
                                        {{ $mySubmission->submitted_at->format('d M, H:i') }}
                                    </span>
                                </div>

                                @if($mySubmission->isLate())
                                    <p class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                        <i class="ti ti-clock-exclamation"></i> Dikumpulkan Melebihi Deadline
                                    </p>
                                @endif
                            </div>

                            <!-- Grade & Feedback Section if graded -->
                            @if($mySubmission->isGraded())
                                <div class="p-4 bg-purple-500/10 border border-purple-500/20 rounded-xl space-y-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                                        Hasil Penilaian Mentor / Admin
                                    </p>
                                    <div class="text-3xl font-extrabold text-purple-700 dark:text-purple-300 font-mono">
                                        {{ $mySubmission->score }} <span class="text-xs font-normal text-purple-500">/ 100</span>
                                    </div>
                                    @if($mySubmission->feedback)
                                        <div class="pt-2 border-t border-purple-500/20 text-xs text-slate-700 dark:text-slate-300 italic">
                                            "{{ $mySubmission->feedback }}"
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl space-y-1">
                                <span class="text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                                    <i class="ti ti-alert-triangle text-base"></i> Belum Mengumpulkan
                                </span>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Harap kumpulkan file atau link tugas sebelum deadline berakhir.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Upload Submission -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-6 shadow-sm space-y-5">
                        <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="ti ti-upload text-indigo-600 dark:text-indigo-400"></i>
                                {{ $mySubmission ? 'Perbarui Pengumpulan Tugas' : 'Form Pengumpulan Tugas' }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Unggah dokumen/gambar atau cantumkan link repositori GitHub / Google Drive
                            </p>
                        </div>

                        <form method="POST" action="{{ route('assignments.submit', $assignment->id) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <!-- File Upload -->
                            <div>
                                <label for="submission_file" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Unggah File Tugas (PDF, DOCX, ZIP, Gambar, Maks 10MB)
                                </label>
                                <input type="file" id="submission_file" name="submission_file" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300">
                                @if($mySubmission && $mySubmission->submission_file)
                                    <div class="mt-1.5 text-[11px] text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                                        <i class="ti ti-file-check"></i> File terunggah saat ini:
                                        <a href="{{ Storage::url($mySubmission->submission_file) }}" target="_blank" class="underline hover:text-indigo-500 font-semibold">Lihat File</a>
                                    </div>
                                @endif
                                @error('submission_file')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submission URL -->
                            <div>
                                <label for="submission_url" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Link Repositori / Dokumentasi Eksternal (Opsional)
                                </label>
                                <input type="url" id="submission_url" name="submission_url" value="{{ old('submission_url', $mySubmission->submission_url ?? '') }}" placeholder="https://github.com/username/project atau link Google Drive" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500">
                                @error('submission_url')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Catatan Tambahan (Opsional)
                                </label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Tuliskan pesan singkat atau instruksi khusus mengenai hasil pengerjaan..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500">{{ old('notes', $mySubmission->notes ?? '') }}</textarea>
                                @error('notes')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                                    <i class="ti ti-send text-base"></i> {{ $mySubmission ? 'Perbarui Pengumpulan' : 'Kirim Tugas Sekarang' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
