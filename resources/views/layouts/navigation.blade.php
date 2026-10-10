<!-- Mobile Overlay Backdrop -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm md:hidden" 
     x-cloak></div>

<!-- Sidebar Drawer -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed md:static inset-y-0 left-0 z-50 w-72 md:w-64 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-r border-slate-200/80 dark:border-slate-800/80 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 shrink-0 shadow-2xl md:shadow-none">
    
    <!-- Brand / Logo Header -->
    <div class="h-16 px-5 sm:px-6 flex items-center justify-between border-b border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/30">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-base shadow-sm shadow-indigo-500/30 group-hover:scale-105 transition-transform">
                <i class="ti ti-scan"></i>
            </div>
            <div>
                <span class="font-bold text-sm tracking-tight text-slate-900 dark:text-white block group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">SmartAttend</span>
                <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 font-mono tracking-wider block leading-none">ENTERPRISE OS</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <i class="ti ti-x text-xl"></i>
        </button>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">
        <!-- Main Navigation Section -->
        <div>
            <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Main Menu</p>
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-dashboard text-base"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Scan Wajah Kamera -->
                <a href="{{ route('attendance.create') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('attendance.create') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-camera text-base"></i>
                    <span>Scan Presensi Wajah</span>
                </a>

                <!-- Rekap Absensi -->
                <a href="{{ route('attendance.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('attendance.index') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="ti ti-file-text text-base"></i>
                    <span>Rekap Presensi</span>
                </a>

                <!-- Manajemen Tugas / Assignment -->
                @php
                    $navUser = Auth::user();
                    $assBadgeCount = 0;
                    if ($navUser) {
                        if ($navUser->canManageAssignments()) {
                            $assBadgeCount = \App\Models\AssignmentSubmission::whereNull('score')->count();
                        } else {
                            $assBadgeCount = \App\Models\Assignment::where('is_active', true)
                                ->where('deadline', '>=', now())
                                ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $navUser->id))
                                ->count();
                        }
                    }
                @endphp
                <a href="{{ route('assignments.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 text-xs font-medium rounded-xl transition duration-150 {{ (request()->routeIs('assignments.*') && !request()->routeIs('assignments.rekap-bulanan*')) ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 font-semibold shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="ti ti-notebook text-base"></i>
                        <span>Manajemen Tugas</span>
                    </div>
                    @if($assBadgeCount > 0)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $navUser->canManageAssignments() ? 'bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/30' : 'bg-rose-500 text-white shadow-xs animate-pulse' }}">
                            {{ $assBadgeCount }}
                        </span>
                    @endif
                </a>

                @if(in_array(auth()->user()->role, ['mentor', 'owner']))
                    <!-- Rekap Tugas Bulanan (Khusus Mentor & Owner) -->
                    <a href="{{ route('assignments.rekap-bulanan') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('assignments.rekap-bulanan*') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 font-semibold shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="ti ti-report-analytics text-base"></i>
                        <span>Rekap Tugas Bulanan</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Operations & Finance (Owner, Admin, CS, Mentor) -->
        @if(Auth::user()->canManageOrders() || Auth::user()->canManageFinance())
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Operasional & Kas</p>
                <nav class="space-y-1">
                    @if(Auth::user()->canManageOrders())
                        <a href="{{ route('orders.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('orders.index') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-shopping-cart text-base"></i>
                            <span>Order Book</span>
                        </a>

                        <a href="{{ route('orders.recap') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('orders.recap') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-file-analytics text-base"></i>
                            <span>Rekap Order Bulanan</span>
                        </a>
                    @endif

                    @if(Auth::user()->canManageFinance())
                        <a href="{{ route('finance.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('finance.index') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-wallet text-base"></i>
                            <span>Arus Kas (Financial)</span>
                        </a>
                    @endif
                </nav>
            </div>
        @endif

        <!-- Media & Marketing (Owner, Admin, Mentor, Sosmed) -->
        @if(Auth::user()->canManageContent())
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Media Planner</p>
                <nav class="space-y-1">
                    <a href="{{ route('content.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('content.*') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="ti ti-calendar-event text-base"></i>
                        <span>Kalender Konten</span>
                    </a>
                </nav>
            </div>
        @endif

        <!-- Owner & System Controls -->
        @if(Auth::user()->isOwner() || Auth::user()->canViewAuditLogs())
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">System Controls</p>
                <nav class="space-y-1">
                    @if(Auth::user()->isOwner())
                        <a href="{{ route('finance.profit_loss') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('finance.profit_loss') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-chart-dots text-base"></i>
                            <span>Laba Rugi Perusahaan</span>
                        </a>
                    @endif

                    @if(Auth::user()->canViewAuditLogs())
                        <a href="{{ route('audit_logs.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('audit_logs.index') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                            <i class="ti ti-shield-lock text-base"></i>
                            <span>Audit Trail Logs</span>
                        </a>
                    @endif
                </nav>
            </div>
        @endif

        <!-- Admin & IT User Management -->
        @if(Auth::user()->canManageUsers())
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Sistem & Pengguna</p>
                <nav class="space-y-1">
                    <a href="{{ route('users.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-lg transition duration-150 {{ request()->routeIs('users.*') ? 'bg-indigo-50 dark:bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="ti ti-users text-base"></i>
                        <span>User & Data Wajah</span>
                    </a>
                </nav>
            </div>
        @endif
    </div>

    <!-- User Profile Footer -->
    <div class="p-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-950/40">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300 font-bold text-xs border border-slate-300 dark:border-slate-700 shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-900 dark:text-white truncate leading-tight flex items-center gap-1.5">
                        <span class="truncate">{{ Auth::user()->name }}</span>
                        <span class="px-1.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wider rounded border shrink-0
                            @if(Auth::user()->isOwner()) bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30
                            @elseif(Auth::user()->isAdmin()) bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30
                            @elseif(Auth::user()->isMentor()) bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/30
                            @else bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/30 @endif">
                            {{ strtoupper(Auth::user()->role) }}
                        </span>
                    </p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate leading-tight mt-0.5">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout Sesi" class="px-2.5 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 rounded-md text-xs font-semibold flex items-center gap-1 transition">
                    <i class="ti ti-power text-xs"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>

</aside>
