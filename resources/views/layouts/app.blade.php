<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartAttend Pro') }} - Enterprise Management System</title>

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
            /* Custom scrollbars for clean Vercel/Linear feel */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: #090d16;
            }
            ::-webkit-scrollbar-thumb {
                background: #1e293b;
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #334155;
            }
        </style>
    </head>
    <body class="h-full font-sans antialiased bg-slate-950 text-slate-100 selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">
        
        <div class="min-h-screen flex flex-col md:flex-row">
            <!-- Sidebar Navigation (Desktop & Mobile Drawer) -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-950">
                <!-- Top Header Bar -->
                <header class="sticky top-0 z-30 bg-slate-950/80 backdrop-blur border-b border-slate-800/80 px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <!-- Mobile Sidebar Hamburger Toggle -->
                        <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 border border-slate-800 transition">
                            <i class="ti ti-menu-2 text-xl"></i>
                        </button>

                        @isset($header)
                            <div>{{ $header }}</div>
                        @else
                            <h1 class="text-base font-semibold text-white">SmartAttend Pro Enterprise</h1>
                        @endisset
                    </div>

                    <!-- Header Quick Actions & User Pill -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('attendance.create') }}" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs rounded-lg transition shadow-sm">
                            <i class="ti ti-camera text-sm"></i> Presensi Wajah
                        </a>

                        @php
                            $roleColors = [
                                'owner' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                'admin' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                'mentor' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'karyawan' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'siswa' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
                            ];
                            $userRole = Auth::user()->role ?? 'karyawan';
                        @endphp

                        <div class="flex items-center gap-2 pl-2 border-l border-slate-800">
                            <span class="px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider rounded-md border {{ $roleColors[$userRole] ?? 'bg-slate-800 text-slate-300 border-slate-700' }}">
                                {{ $userRole }}
                            </span>

                            <!-- Explicit Logout / Ganti Akun Button -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" title="Logout dari Sesi Ini" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-md text-xs font-semibold transition">
                                    <i class="ti ti-logout text-sm"></i>
                                    <span class="hidden sm:inline">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Flash Toast Notifications -->
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 3500,
                                background: '#0f172a',
                                color: '#f8fafc',
                                timerProgressBar: true
                            });
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: "{{ session('error') }}",
                                showConfirmButton: false,
                                timer: 4000,
                                background: '#0f172a',
                                color: '#f8fafc',
                                timerProgressBar: true
                            });
                        });
                    </script>
                @endif

                <!-- Main Content Body -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="border-t border-slate-900 py-4 px-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p>&copy; {{ date('Y') }} SmartAttend Enterprise &bull; Modular Laravel Solution</p>
                    <div class="flex items-center gap-4 text-slate-400">
                        <span>v2.5.0</span>
                        <span>&bull;</span>
                        <span>Role: {{ strtoupper(Auth::user()->role) }}</span>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
