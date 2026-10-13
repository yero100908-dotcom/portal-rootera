<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartAttend Pro') }} - Enterprise Management System</title>

        <!-- Anti-FOUC Theme Initialization Script -->
        <script>
            (function() {
                const theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        <!-- Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tabler Icons CDN -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

        <!-- FullCalendar CDN -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Scripts & Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            [x-cloak] { display: none !important; }
            /* Custom scrollbars for clean dual theme feel */
            html.dark ::-webkit-scrollbar-track {
                background: #090d16;
            }
            html.dark ::-webkit-scrollbar-thumb {
                background: #1e293b;
                border-radius: 4px;
            }
            html.dark ::-webkit-scrollbar-thumb:hover {
                background: #334155;
            }
            html:not(.dark) ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            html:not(.dark) ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }
            html:not(.dark) ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    </head>
    <body class="min-h-[100dvh] font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 selection:bg-indigo-500 selection:text-white transition-colors duration-200 overflow-x-hidden" 
          x-data="{ sidebarOpen: false, isDarkMode: document.documentElement.classList.contains('dark') }"
          :class="{ 'overflow-hidden': sidebarOpen }">
        
        <div class="min-h-[100dvh] flex flex-col md:flex-row">
            <!-- Sidebar Navigation (Desktop & Mobile Drawer) -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950 transition-colors duration-200 overflow-x-hidden">
                <!-- Top Header Bar -->
                <header class="sticky top-0 z-30 bg-white/85 dark:bg-slate-950/85 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 px-2.5 sm:px-6 h-14 sm:h-16 shrink-0 flex items-center justify-between gap-2 sm:gap-4 transition-colors duration-200">
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 overflow-hidden">
                        <!-- Mobile Sidebar Hamburger Toggle (Touch-friendly 44x44px) -->
                        <button @click="sidebarOpen = true" aria-label="Open Navigation Menu" class="md:hidden touch-target p-2 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 transition shrink-0">
                            <i class="ti ti-menu-2 text-xl"></i>
                        </button>

                        @isset($header)
                            <div class="min-w-0 flex-1 overflow-hidden flex items-center">{{ $header }}</div>
                        @else
                            <h1 class="text-sm sm:text-base font-semibold text-slate-900 dark:text-white truncate">SmartAttend Pro</h1>
                        @endisset
                    </div>

                    <!-- Header Quick Actions, Theme Switcher & User Pill -->
                    <div class="flex items-center gap-1 sm:gap-2.5 shrink-0">
                        <a href="{{ route('attendance.create') }}" class="hidden lg:inline-flex items-center gap-2 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-lg transition shadow-sm">
                            <i class="ti ti-camera text-sm"></i> Presensi Wajah
                        </a>

                        <!-- Theme Switcher Toggle Button -->
                        <button 
                            @click="
                                isDarkMode = !isDarkMode;
                                if (isDarkMode) {
                                    document.documentElement.classList.add('dark');
                                    localStorage.setItem('theme', 'dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.setItem('theme', 'light');
                                }
                            " 
                            type="button"
                            title="Toggle Light / Dark Theme" 
                            class="p-2 sm:p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 transition duration-200 flex items-center justify-center shrink-0"
                        >
                            <template x-if="isDarkMode">
                                <i class="ti ti-sun text-amber-400 text-sm sm:text-base"></i>
                            </template>
                            <template x-if="!isDarkMode">
                                <i class="ti ti-moon-stars text-indigo-600 text-sm sm:text-base"></i>
                            </template>
                        </button>

                        @php
                            $roleColors = [
                                'owner' => 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/30',
                                'admin' => 'bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-500/30',
                                'mentor' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
                                'karyawan' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30',
                                'siswa' => 'bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border-cyan-500/30',
                            ];
                            $userRole = Auth::user()->role ?? 'karyawan';
                        @endphp

                        <div class="flex items-center gap-1 sm:gap-2 pl-1.5 sm:pl-2 border-l border-slate-200 dark:border-slate-800 shrink-0">
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider rounded-md border {{ $roleColors[$userRole] ?? 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700' }}">
                                {{ $userRole }}
                            </span>

                            <!-- Explicit Logout / Ganti Akun Button -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" title="Logout dari Sesi Ini" class="inline-flex items-center justify-center p-2 sm:px-3 sm:py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 rounded-md text-xs font-semibold transition shrink-0">
                                    <i class="ti ti-logout text-sm sm:text-base"></i>
                                    <span class="hidden sm:inline ml-1.5">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Flash Toast Notifications -->
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const isDark = document.documentElement.classList.contains('dark');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 3500,
                                background: isDark ? '#0f172a' : '#ffffff',
                                color: isDark ? '#f8fafc' : '#0f172a',
                                timerProgressBar: true
                            });
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const isDark = document.documentElement.classList.contains('dark');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: "{{ session('error') }}",
                                showConfirmButton: false,
                                timer: 4000,
                                background: isDark ? '#0f172a' : '#ffffff',
                                color: isDark ? '#f8fafc' : '#0f172a',
                                timerProgressBar: true
                            });
                        });
                    </script>
                @endif

                <!-- Main Content Body -->
                <main class="flex-1 px-3 py-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-4 sm:space-y-6">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="border-t border-slate-200 dark:border-slate-900 py-4 px-6 pb-safe-bottom text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 transition-colors duration-200">
                    <p>&copy; {{ date('Y') }} SmartAttend Enterprise &bull; Modular Laravel Solution</p>
                    <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400">
                        <span>v2.5.0</span>
                        <span>&bull;</span>
                        <span>Role: {{ strtoupper(Auth::user()->role) }}</span>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
