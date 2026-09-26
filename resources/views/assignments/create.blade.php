<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="ti ti-plus text-indigo-600 dark:text-indigo-400"></i> Buat Tugas Baru
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Tambahkan tugas baru untuk karyawan/siswa beserta file instruksi & deadline
                </p>
            </div>
            <a href="{{ route('assignments.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg border border-slate-200 dark:border-slate-700 transition flex items-center gap-1.5">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-6 shadow-sm">
            <form method="POST" action="{{ route('assignments.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Judul Tugas -->
                <div>
                    <label for="title" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Judul Tugas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Modul 1 - Desain Mockup UI & Laporan Kas" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500 @error('title') border-rose-500 @enderror">
                    @error('title')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi / Instruksi -->
                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Deskripsi / Instruksi Tugas <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="5" required placeholder="Tuliskan petunjuk pengerjaan tugas, kriteria penilaian, format file, atau link pendukung..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500 @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
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
                        <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline') }}" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500 @error('deadline') border-rose-500 @enderror">
                        @error('deadline')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Attachment -->
                    <div>
                        <label for="file_attachment" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            File Lampiran / Panduan (Opsional)
                        </label>
                        <input type="file" id="file_attachment" name="file_attachment" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-indigo-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300">
                        <p class="text-[10px] text-slate-500 mt-1">Format disukai: PDF, DOCX, ZIP, PNG, JPG (Maks. 10MB)</p>
                        @error('file_attachment')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Checkbox Status Aktif -->
                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Publikasikan & Aktifkan Tugas Ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('assignments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <i class="ti ti-check text-base"></i> Simpan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
