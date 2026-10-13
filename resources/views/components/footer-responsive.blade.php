<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Responsive 4-Column Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
        
        <!-- Kolom 1: Brand & Ringkasan -->
        <div class="space-y-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white">
                    <i class="ti ti-shield-check text-lg"></i>
                </div>
                <span class="font-extrabold text-base text-slate-900 dark:text-white">RooteraPortal</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Platform enterprise terpadu untuk presensi biometrik, manajemen penugasan harian, dan analitik operasional terdistribusi.
            </p>
            <div class="flex items-center gap-2 pt-2">
                <a href="#" aria-label="Portal Documentation" class="touch-target w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <i class="ti ti-book text-lg"></i>
                </a>
                <a href="#" aria-label="Github Repo" class="touch-target w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <i class="ti ti-brand-github text-lg"></i>
                </a>
                <a href="#" aria-label="Bantuan Layanan" class="touch-target w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    <i class="ti ti-headset text-lg"></i>
                </a>
            </div>
        </div>

        <!-- Kolom 2: Modul Utama -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Modul Sistem</h4>
            <ul class="space-y-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                <li><a href="{{ route('attendance.create') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Presensi Wajah AI</a></li>
                <li><a href="{{ route('attendance.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Rekap Presensi & Audit</a></li>
                <li><a href="{{ route('assignments.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Manajemen Tugas & Grading</a></li>
                <li><a href="{{ route('orders.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Buku Order & Keuangan</a></li>
            </ul>
        </div>

        <!-- Kolom 3: Pengguna & Peran -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Akses Pengguna</h4>
            <ul class="space-y-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                <li><span class="text-slate-500">Administrator & IT Security</span></li>
                <li><span class="text-slate-500">Mentor & Koordinator Magang</span></li>
                <li><span class="text-slate-500">Karyawan & Staff Operasional</span></li>
                <li><span class="text-slate-500">Siswa & Mahasiswa Magang</span></li>
            </ul>
        </div>

        <!-- Kolom 4: Keamanan & Status -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Keamanan & Layanan</h4>
            <ul class="space-y-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                <li><span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 font-medium"><i class="ti ti-shield-check"></i> Enkripsi Data AES-256</span></li>
                <li><span class="text-slate-500">Anti-Spoofing Facial Engine</span></li>
                <li><span class="text-slate-500">Geofencing Real-Time GPS</span></li>
                <li><span class="text-slate-500">Audit Trail Multi-Level Log</span></li>
            </ul>
        </div>

    </div>

    <!-- Bottom Bar & iOS Safe Padding -->
    <div class="mt-10 pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 pb-safe-bottom">
        <p>&copy; {{ date('Y') }} PT Rootera Solusi Digital &bull; Enterprise Attendance OS.</p>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Semua Layanan Beroperasi Normal</span>
        </div>
    </div>
</div>
