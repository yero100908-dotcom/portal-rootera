<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartAttend Enterprise OS') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tabler Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-['Plus_Jakarta_Sans',sans-serif] bg-slate-950 text-slate-100 antialiased selection:bg-indigo-500 selection:text-white min-h-screen relative overflow-x-hidden flex flex-col justify-center items-center py-12 px-4">
        
        <!-- Ambient Glowing Background Elements -->
        <div class="fixed top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="fixed bottom-10 right-10 w-[350px] h-[350px] bg-purple-600/10 rounded-full blur-[100px] pointer-events-none"></div>

        <!-- Brand Header Logo -->
        <div class="mb-8 text-center relative z-10">
            <a href="/" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 group-hover:scale-105 transition-transform duration-300 shadow-lg shadow-indigo-500/10">
                    <i class="ti ti-scan text-2xl"></i>
                </div>
                <div class="text-left">
                    <span class="font-extrabold text-xl tracking-tight text-white block leading-none">SmartAttend</span>
                    <span class="text-[10px] text-indigo-400 font-semibold tracking-wider block uppercase mt-1">Enterprise OS</span>
                </div>
            </a>
        </div>

        <!-- Main Card Slot Container -->
        <div class="w-full max-w-md bg-slate-900/80 border border-slate-800/80 rounded-2xl p-8 shadow-2xl backdrop-blur-xl relative z-10">
            {{ $slot }}
        </div>

        <!-- Footer Caption -->
        <p class="mt-8 text-center text-xs text-slate-500 relative z-10">
            &copy; {{ date('Y') }} SmartAttend Enterprise OS &bull; Secure Authentication System
        </p>
    </body>
</html>
