<nav x-data="{ open: false }" class="bg-slate-900 border-b border-slate-800 text-white shadow-lg sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center shadow-md">
                            <i class="ti ti-scan text-white text-xl"></i>
                        </div>
                        <span class="font-bold text-lg tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-300">
                            SmartAttend Pro
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:flex sm:items-center">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="ti ti-dashboard text-base me-1"></i> Dashboard
                    </a>

                    <!-- Modul Presensi -->
                    <a href="{{ route('attendance.create') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('attendance.create') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="ti ti-camera text-base me-1"></i> Scan Wajah
                    </a>
                    <a href="{{ route('attendance.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('attendance.index') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="ti ti-file-text text-base me-1"></i> Rekap Presensi
                    </a>

                    <!-- Modul Pembukuan & Order (Owner & Admin) -->
                    @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                        <a href="{{ route('orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('orders.*') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="ti ti-shopping-cart text-base me-1"></i> Order Book
                        </a>
                        <a href="{{ route('finance.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('finance.index') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="ti ti-wallet text-base me-1"></i> Arus Kas
                        </a>
                    @endif

                    <!-- Modul Kalender Konten (Owner, Admin, Mentor) -->
                    @if(Auth::user()->isOwner() || Auth::user()->isAdmin() || Auth::user()->isMentor())
                        <a href="{{ route('content.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('content.*') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="ti ti-calendar-event text-base me-1"></i> Kalender Konten
                        </a>
                    @endif

                    <!-- Owner Special Features -->
                    @if(Auth::user()->isOwner())
                        <a href="{{ route('finance.profit_loss') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('finance.profit_loss') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="ti ti-chart-dots text-base me-1"></i> Laba Rugi
                        </a>
                    @endif

                    <!-- User Management (Owner & Admin) -->
                    @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="ti ti-users text-base me-1"></i> User & Wajah
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Profile & Role Badge -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                @php
                    $roleColors = [
                        'owner' => 'bg-purple-500/20 text-purple-300 border-purple-500/40',
                        'admin' => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
                        'mentor' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                        'karyawan' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                        'siswa' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40',
                    ];
                    $userRole = Auth::user()->role ?? 'karyawan';
                @endphp
                <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wider rounded-full border {{ $roleColors[$userRole] ?? 'bg-slate-700 text-slate-300 border-slate-600' }}">
                    <i class="ti ti-shield-check text-xs me-1"></i> {{ $userRole }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-slate-700 text-sm leading-4 font-medium rounded-lg text-slate-200 bg-slate-800 hover:bg-slate-700 focus:outline-none transition ease-in-out duration-150">
                            <span class="me-1">{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(Auth::user()->isOwner())
                            <x-dropdown-link :href="route('audit_logs.index')">
                                <i class="ti ti-shield-lock me-2"></i> Audit Logs
                            </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="ti ti-user me-2"></i> Edit Profile
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <i class="ti ti-power text-red-400 me-2"></i> Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-900 border-b border-slate-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-800">Dashboard</a>
            <a href="{{ route('attendance.create') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-800">Scan Wajah</a>
            <a href="{{ route('attendance.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-800">Rekap Presensi</a>
            @if(Auth::user()->isOwner() || Auth::user()->isAdmin())
                <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-800">Order Book</a>
                <a href="{{ route('finance.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-800">Arus Kas</a>
            @endif
            @if(Auth::user()->isOwner() || Auth::user()->isAdmin() || Auth::user()->isMentor())
                <a href="{{ route('content.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-800">Kalender Konten</a>
            @endif
            @if(Auth::user()->isOwner())
                <a href="{{ route('finance.profit_loss') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-slate-800">Laba Rugi</a>
            @endif
        </div>
    </div>
</nav>
