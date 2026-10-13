<section id="features" class="space-y-10 sm:space-y-12">
    <!-- Section Title & Subtitle -->
    <div class="text-center max-w-3xl mx-auto space-y-3">
        <h2 class="text-2xl xs:text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Fitur Cerdas Skala Enterprise
        </h2>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
            Didesain khusus untuk tata kelola operasional dengan akurasi biometrik tinggi, koordinasi tim terpusat, dan rekapitulasi data instan.
        </p>
    </div>

    <!-- Responsive Multi-Column Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
        
        <!-- Card 1: Presensi Wajah -->
        <article class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-indigo-500/50 transition-all group flex flex-col justify-between">
            <div class="space-y-3.5">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="ti ti-scan-eye"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Face Recognition Anti-Spoofing</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Verifikasi liveness presensi langsung melalui browser tanpa instalasi aplikasi berat, terlindung dari manipulasi foto.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                <span>Modul Biometrik Aktif</span>
                <i class="ti ti-chevron-right"></i>
            </div>
        </article>

        <!-- Card 2: Geofencing GPS -->
        <article class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-indigo-500/50 transition-all group flex flex-col justify-between">
            <div class="space-y-3.5">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="ti ti-map-pin-check"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Geofencing Multi-Radius</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Validasi radius GPS dinamis per lokasi kantor cabang, remote site, atau wilayah operasional lapangan dengan toleransi akurat.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                <span>Validasi Lokasi Terintegrasi</span>
                <i class="ti ti-chevron-right"></i>
            </div>
        </article>

        <!-- Card 3: Manajemen Tugas & Penilaian -->
        <article class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-indigo-500/50 transition-all group flex flex-col justify-between sm:col-span-2 lg:col-span-1">
            <div class="space-y-3.5">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="ti ti-notebook"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Workflow Tugas & Penilaian</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Distribusi tugas harian, pengumpulan berkas terenkripsi, grading mentor, dan rekap penilaian bulanan tanpa hambatan.
                </p>
            </div>
            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 text-xs font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                <span>Sistem Penugasan Google-Classroom Style</span>
                <i class="ti ti-chevron-right"></i>
            </div>
        </article>

    </div>

    <!-- Responsive Data Section (Desktop Table / Mobile Card Transformation) -->
    <div id="activity-log" class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-6 lg:p-8 shadow-sm">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 sm:pb-5 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ti ti-activity text-indigo-600"></i>
                    <span>Log Aktivitas Presensi Terkini</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring status verifikasi presensi harian karyawan & siswa magang</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80 w-fit">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Live Feed</span>
            </span>
        </div>

        <!-- Desktop View: Elegant Responsive Table (hidden on mobile < 640px) -->
        <div class="hidden sm:block overflow-x-auto mt-4">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-xs uppercase text-slate-400 font-semibold tracking-wider">
                        <th class="py-3.5 px-4">Nama Pengguna</th>
                        <th class="py-3.5 px-4">Waktu Presensi</th>
                        <th class="py-3.5 px-4">Titik Geofence</th>
                        <th class="py-3.5 px-4">Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 shrink-0">RP</span>
                            <span class="truncate">Rizal Pratama (IT Dev)</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs">07:54:12 WIB</td>
                        <td class="py-3.5 px-4 text-xs">Head Office (Radius 12m)</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                <i class="ti ti-check text-xs"></i> Tepat Waktu
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center text-xs font-bold text-amber-600 dark:text-amber-400 shrink-0">SN</span>
                            <span class="truncate">Siti Nurhaliza (Content)</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs">08:04:45 WIB</td>
                        <td class="py-3.5 px-4 text-xs">Studio Kreatif (Radius 8m)</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400">
                                <i class="ti ti-clock text-xs"></i> Terlambat 4m
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-900/60 flex items-center justify-center text-xs font-bold text-cyan-600 dark:text-cyan-400 shrink-0">BA</span>
                            <span class="truncate">Budi Arifin (Magang)</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs">07:48:30 WIB</td>
                        <td class="py-3.5 px-4 text-xs">Head Office (Radius 15m)</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                <i class="ti ti-check text-xs"></i> Tepat Waktu
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile View: Touch-Friendly Card Transformation (visible on mobile < 640px) -->
        <div class="sm:hidden space-y-3 mt-4">
            
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-[11px] font-bold text-indigo-600 dark:text-indigo-400 shrink-0">RP</span>
                        <span class="font-bold text-sm text-slate-900 dark:text-white truncate">Rizal Pratama (IT Dev)</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 shrink-0">Tepat Waktu</span>
                </div>
                <div class="text-xs text-slate-500 space-y-1.5 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                    <div class="flex items-center justify-between">
                        <span>Waktu:</span>
                        <span class="font-mono font-medium text-slate-700 dark:text-slate-300">07:54:12 WIB</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Lokasi:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">Head Office (Radius 12m)</span>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center text-[11px] font-bold text-amber-600 dark:text-amber-400 shrink-0">SN</span>
                        <span class="font-bold text-sm text-slate-900 dark:text-white truncate">Siti Nurhaliza (Content)</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 shrink-0">Terlambat 4m</span>
                </div>
                <div class="text-xs text-slate-500 space-y-1.5 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                    <div class="flex items-center justify-between">
                        <span>Waktu:</span>
                        <span class="font-mono font-medium text-slate-700 dark:text-slate-300">08:04:45 WIB</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Lokasi:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300">Studio Kreatif (Radius 8m)</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
