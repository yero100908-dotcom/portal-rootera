<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Rootera Portal') }} - Enterprise Attendance & Task OS</title>

    <!-- Anti-FOUC Dark Mode Detection -->
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <!-- Styles & Scripts (Tailwind CSS v3 + Alpine.js via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-[100dvh] flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden selection:bg-indigo-600 selection:text-white transition-colors duration-200"
      x-data="{ mobileNavOpen: false }"
      :class="{ 'overflow-hidden': mobileNavOpen }">

    <!-- Accessibility: Skip To Content -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-lg font-medium text-sm">
        Lewati ke Konten Utama
    </a>

    <!-- Top Sticky Adaptive Header -->
    <header class="sticky top-0 z-40 w-full bg-white/85 dark:bg-slate-950/85 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 transition-colors">
        @include('components.navbar-responsive')
    </header>

    <!-- Main Content Container (Bounded Max Width for Zero Overstretch on 4K) -->
    <main id="main-content" class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 lg:py-12 space-y-12 sm:space-y-16 lg:space-y-20">
        <!-- 1. Hero Section Fluid & Adaptive -->
        @include('components.hero-responsive')

        <!-- 2. Responsive Grid Cards & Mobile-Transformed Data Table -->
        @include('components.features-and-table')

        <!-- 3. Touch-Friendly Ergonomic Form -->
        @include('components.form-responsive')
    </main>

    <!-- Global Responsive Footer -->
    <footer class="w-full bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 mt-auto">
        @include('components.footer-responsive')
    </footer>

</body>
</html>
