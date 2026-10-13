<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 min-w-0">
            <a href="{{ route('assignments.show', $assignment->id) }}" class="p-1 -ml-1 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-lg transition" title="Kembali ke Detail">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight truncate flex items-center gap-1.5">
                <i class="ti ti-edit text-indigo-600 dark:text-indigo-400 shrink-0"></i>
                <span class="truncate">Edit: {{ $assignment->title }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200/80 dark:border-slate-800/80 rounded-2xl p-5 sm:p-7 shadow-xs">
            <form method="POST" action="{{ route('assignments.update', $assignment->id) }}" enctype="multipart/form-data" class="space-y-5" x-data="{ selectedFileName: '', selectedFileSize: '' }">
                @csrf
                @method('PUT')

                <!-- Judul Tugas -->
                <div>
                    <label for="title" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Judul Tugas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $assignment->title) }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 @error('title') border-rose-500 @enderror">
                    @error('title')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi / Instruksi -->
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Deskripsi & Instruksi Tugas <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs sm:text-sm rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 @error('description') border-rose-500 @enderror">{{ old('description', $assignment->description) }}</textarea>
                    @error('description')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadline & File Attachment (Responsive 1-col mobile, 2-col tablet/desktop) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Batas Waktu Pengumpulan (Deadline) <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline', $assignment->deadline->format('Y-m-d\TH:i')) }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs sm:text-sm font-mono rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 @error('deadline') border-rose-500 @enderror">
                        @error('deadline')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Attachment -->
                    <div>
                        <label for="file_attachment" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Ganti File Lampiran / Panduan
                        </label>
                        <input type="file" id="file_attachment" name="file_attachment" 
                               @change="if($event.target.files.length > 0) { selectedFileName = $event.target.files[0].name; selectedFileSize = ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB'; }"
                               class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300">
                        <template x-if="selectedFileName">
                            <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold mt-1.5 flex items-center gap-1">
                                <i class="ti ti-check"></i>
                                <span x-text="selectedFileName + ' (' + selectedFileSize + ')'"></span>
                            </p>
                        </template>
                        @if($assignment->file_attachment)
                            <div class="mt-1.5 text-[11px] text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                                <i class="ti ti-paperclip"></i>
                                <span>File aktif saat ini:</span>
                                <a href="{{ Storage::url($assignment->file_attachment) }}" target="_blank" class="underline hover:text-indigo-500 font-bold">Unduh Lampiran</a>
                            </div>
                        @endif
                        @error('file_attachment')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Modern Switch Toggle Status Aktif -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $assignment->is_active) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden peer-focus:ring-2 peer-focus:ring-indigo-500/30 rounded-full peer dark:bg-slate-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Publikasikan & Aktifkan Tugas Ini</span>
                            <span class="text-[10px] text-slate-400 block">Jika dinonaktifkan, tugas tidak akan muncul di daftar tugas tim</span>
                        </div>
                    </label>
                </div>

                <!-- Responsive Submit Buttons -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5">
                    <a href="{{ route('assignments.show', $assignment->id) }}" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-semibold text-xs rounded-xl shadow-sm shadow-indigo-500/25 transition active:scale-95">
                        <i class="ti ti-check text-base"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
