<section class="relative overflow-hidden py-4 sm:py-8 lg:py-14">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
        
        <!-- Left Column: Fluid Copywriting & Actions -->
        <div class="lg:col-span-7 space-y-5 sm:space-y-6 text-center lg:text-left">
            <!-- Pill Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/80 text-indigo-700 dark:text-indigo-300 text-xs sm:text-sm font-semibold">
                <span class="flex h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                <span>Portal Presensi Biometrik & Manajemen Tugas v2.5</span>
            </div>

            <!-- Fluid Responsive Heading -->
            <h1 class="text-3xl xs:text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.15]">
                Solusi Enterprise Terpadu untuk <span class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-sky-500 bg-clip-text text-transparent">Efisiensi Maksimal</span>
            </h1>

            <!-- Supporting Paragraph -->
            <p class="text-sm sm:text-base lg:text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                Kelola kehadiran biometrik wajah, penugasan harian multi-level, kalender media, dan analisis arus kas dalam satu sistem cloud adaptif di semua resolusi perangkat.
            </p>

            <!-- Responsive Button Group -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center lg:justify-start gap-3 sm:gap-4 pt-1 sm:pt-2">
                <a href="{{ route('attendance.create') }}" class="touch-target px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold text-sm sm:text-base rounded-xl shadow-lg shadow-indigo-600/25 transition-all text-center flex items-center justify-center gap-2">
                    <i class="ti ti-camera text-lg"></i>
                    <span>Coba Presensi Wajah</span>
                </a>
                <a href="{{ route('login') }}" class="touch-target px-6 py-3.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 font-semibold text-sm sm:text-base rounded-xl transition text-center flex items-center justify-center gap-2">
                    <i class="ti ti-login text-indigo-600 text-lg"></i>
                    <span>Masuk ke Dashboard</span>
                </a>
            </div>

            <!-- Operational Metrics Grid -->
            <div class="pt-6 border-t border-slate-200/80 dark:border-slate-800 grid grid-cols-3 gap-2 sm:gap-4 text-center lg:text-left">
                <div class="p-2 sm:p-0">
                    <div class="text-lg xs:text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">99.98%</div>
                    <div class="text-[11px] sm:text-xs text-slate-500">Uptime SLA</div>
                </div>
                <div class="p-2 sm:p-0">
                    <div class="text-lg xs:text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">50k+</div>
                    <div class="text-[11px] sm:text-xs text-slate-500">Log Presensi</div>
                </div>
                <div class="p-2 sm:p-0">
                    <div class="text-lg xs:text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">&lt; 300ms</div>
                    <div class="text-[11px] sm:text-xs text-slate-500">Latency Engine</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Visual Mockup Container (Explicit Aspect Ratio for Zero CLS) -->
        <div class="lg:col-span-5 w-full">
            <div class="relative w-full aspect-[4/3] sm:aspect-[16/10] lg:aspect-[4/3] rounded-2xl sm:rounded-3xl bg-gradient-to-tr from-indigo-500/10 via-slate-100 to-indigo-500/5 dark:from-indigo-950/40 dark:via-slate-900 dark:to-slate-950 border border-slate-200/80 dark:border-slate-800 p-3 sm:p-4 shadow-xl overflow-hidden contain-layout">
                
                <!-- Inner Visual Card Interface -->
                <div class="w-full h-full rounded-xl sm:rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-200/60 dark:border-slate-800/80 p-4 sm:p-6 flex flex-col justify-between overflow-hidden">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        </div>
                        <span class="text-xs font-mono text-slate-400">smartattend.rootera.internal</span>
                    </div>

                    <div class="space-y-3 py-3">
                        <div class="h-3.5 bg-slate-100 dark:bg-slate-800 rounded-md w-3/4"></div>
                        <div class="h-3.5 bg-slate-100 dark:bg-slate-800 rounded-md w-1/2"></div>
                        
                        <div class="grid grid-cols-2 gap-2.5 sm:gap-3 pt-2">
                            <div class="p-3 bg-indigo-50/50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-900/40">
                                <span class="text-[11px] text-slate-500 block">Presensi Hari Ini</span>
                                <span class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400">1,248 Sesi</span>
                            </div>
                            <div class="p-3 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-900/40">
                                <span class="text-[11px] text-slate-500 block">Tepat Waktu</span>
                                <span class="text-base sm:text-lg font-bold text-emerald-600 dark:text-emerald-400">98.4%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] sm:text-xs text-slate-500 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span>Liveness Detection Aktif</span>
                        <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span>Online</span>
                        </span>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>
