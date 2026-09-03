<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartAttend Pro') }} - Platform Enterprise Absensi & Operasional</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen bg-slate-950 flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 py-6 px-4 sm:px-6 lg:px-8 shadow-sm">
                    <div class="max-w-7xl mx-auto flex items-center justify-between">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Alert Toast -->
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 4000,
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
                            timerProgressBar: true
                        });
                    });
                </script>
            @endif

            <!-- Page Content -->
            <main class="flex-1 max-w-7xl w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-slate-900 border-t border-slate-800/60 py-6 text-center text-xs text-slate-400">
                <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p>&copy; {{ date('Y') }} SmartAttend Pro &bull; Senior Laravel Enterprise Architecture</p>
                    <p class="text-slate-500">Versi 2.5.0 &bull; Secure RBAC & Face Recognition API</p>
                </div>
            </footer>
        </div>
    </body>
</html>
