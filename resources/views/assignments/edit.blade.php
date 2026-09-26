<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="ti ti-edit text-indigo-600 dark:text-indigo-400"></i> Edit Tugas
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Perbarui judul, instruksi, tenggat waktu, atau file pendukung tugas
                </p>
            </div>
            <a href="{{ route('assignments.show', $assignment->id) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5">
                <i class="ti ti-arrow-left"></i> Kembali ke Detail
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-6 shadow-sm">
            <form method="POST" action="{{ route('assignments.update', $assignment->id) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Judul Tugas -->
                <div>
                    <label for="title" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Judul Tugas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $assignment->title) }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500 @error('title') border-rose-500 @enderror">
                    @error('title')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi / Instruksi -->
                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Deskripsi / Instruksi Tugas <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="5" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500 @error('description') border-rose-500 @enderror">{{ old('description', $assignment->description) }}</textarea>
                    @error('description')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadline & File Attachment -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Batas Waktu Pengumpulan (Deadline) <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline', $assignment->deadline->format('Y-m-d\TH:i')) }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500 @error('deadline') border-rose-500 @enderror">
                        @error('deadline')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Attachment -->
                    <div>
                        <label for="file_attachment" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Ganti File Lampiran / Panduan
                        </label>
                        <input type="file" id="file_attachment" name="file_attachment" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-indigo-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300">
                        @if($assignment->file_attachment)
                            <div class="mt-1.5 text-[11px] text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                                <i class="ti ti-paperclip"></i> File saat ini:
                                <a href="{{ Storage::url($assignment->file_attachment) }}" target="_blank" class="underline hover:text-indigo-500 font-semibold">Unduh Lampiran</a>
                            </div>
                        @endif
                        @error('file_attachment')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Checkbox Status Aktif -->
                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $assignment->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Publikasikan & Aktifkan Tugas Ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('assignments.show', $assignment->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <i class="ti ti-check text-base"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
