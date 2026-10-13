<nav class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Navigasi Utama">
    <div class="flex items-center justify-between h-16 sm:h-20">
        
        <!-- Brand / Logo -->
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded-xl">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                    <i class="ti ti-shield-check text-xl"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold text-base sm:text-lg tracking-tight text-slate-900 dark:text-white leading-tight">Rootera<span class="text-indigo-600 dark:text-indigo-400">Portal</span></span>
                    <span class="text-[10px] font-bold tracking-widest text-slate-500 uppercase">Enterprise OS</span>
                </div>
            </a>
        </div>

        <!-- Desktop Navigation Links (md:flex) -->
        <div class="hidden md:flex items-center gap-1 lg:gap-2">
            <a href="#features" class="px-3.5 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 rounded-lg transition-colors">
                Fitur Unggulan
            </a>
            <a href="#activity-log" class="px-3.5 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 rounded-lg transition-colors">
                Live Data
            </a>
            <a href="#contact" class="px-3.5 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 rounded-lg transition-colors">
                Konsultasi
            </a>
        </div>

        <!-- Desktop Right CTA & Actions -->
        <div class="hidden md:flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all hover:shadow-indigo-600/40 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="ti ti-layout-dashboard text-base"></i>
                    <span>Buka Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    Masuk
                </a>
                <a href="{{ route('attendance.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all hover:shadow-indigo-600/40 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="ti ti-camera text-base"></i>
                    <span>Presensi Wajah</span>
                </a>
            @endauth
        </div>

        <!-- Mobile Hamburger Trigger (Touch-friendly 44x44px) -->
        <div class="flex items-center gap-2 md:hidden">
            <button @click="mobileNavOpen = true"
                    type="button"
                    aria-label="Buka Menu Navigasi"
                    :aria-expanded="mobileNavOpen.toString()"
                    class="touch-target p-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i class="ti ti-menu-2 text-2xl"></i>
            </button>
        </div>

    </div>

    <!-- Mobile Drawer Overlay & Modal -->
    <div x-show="mobileNavOpen"
         x-cloak
         class="fixed inset-0 z-50 md:hidden"
         role="dialog"
         aria-modal="true"
         aria-label="Menu Navigasi Mobile">
        
        <!-- Backdrop Blur -->
        <div x-show="mobileNavOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileNavOpen = false"
             class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm"></div>

        <!-- Off-canvas Drawer Panel -->
        <div x-show="mobileNavOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-full max-w-xs sm:max-w-sm bg-white dark:bg-slate-900 shadow-2xl p-6 flex flex-col justify-between border-l border-slate-200 dark:border-slate-800">
            
            <div>
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold">
                            <i class="ti ti-shield-check text-lg"></i>
                        </div>
                        <span class="font-bold text-slate-900 dark:text-white">Menu Utama</span>
                    </div>
                    <button @click="mobileNavOpen = false"
                            type="button"
                            aria-label="Tutup Menu"
                            class="touch-target p-2 rounded-lg text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="ti ti-x text-2xl"></i>
                    </button>
                </div>

                <!-- Drawer Links -->
                <nav class="mt-6 flex flex-col space-y-1">
                    <a href="#features" @click="mobileNavOpen = false" class="touch-target w-full justify-start px-4 py-3 text-base font-medium text-slate-800 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 rounded-xl transition">
                        <i class="ti ti-apps mr-3 text-lg text-indigo-500"></i> Fitur Unggulan
                    </a>
                    <a href="#activity-log" @click="mobileNavOpen = false" class="touch-target w-full justify-start px-4 py-3 text-base font-medium text-slate-800 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 rounded-xl transition">
                        <i class="ti ti-activity mr-3 text-lg text-indigo-500"></i> Live Activity
                    </a>
                    <a href="#contact" @click="mobileNavOpen = false" class="touch-target w-full justify-start px-4 py-3 text-base font-medium text-slate-800 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-600 rounded-xl transition">
                        <i class="ti ti-mail mr-3 text-lg text-indigo-500"></i> Konsultasi
                    </a>
                </nav>
            </div>

            <!-- Drawer Bottom CTA -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3 pb-safe-bottom">
                @auth
                    <a href="{{ route('dashboard') }}" class="touch-target w-full text-center py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-md transition">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="touch-target w-full text-center py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                        Masuk Akun
                    </a>
                    <a href="{{ route('attendance.create') }}" class="touch-target w-full text-center py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-md transition">
                        Presensi Wajah
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>
