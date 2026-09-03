<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-users text-indigo-400"></i> Manajemen User & Data Wajah
            </h2>
            <p class="text-sm text-slate-400 mt-1">Kelola pengguna sistem, pembagian role (Owner, Admin, Mentor, Karyawan, Siswa), dan pendaftaran wajah</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <i class="ti ti-user-plus text-lg"></i> Tambah User Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Search Bar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama user, email, atau no telepon..." class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="w-full md:w-48">
                    <select name="role" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Role --</option>
                        <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="mentor" {{ request('role') === 'mentor' ? 'selected' : '' }}>Mentor</option>
                        <option value="karyawan" {{ request('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                        <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl transition shadow">
                    <i class="ti ti-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Nama & Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">No Telepon</th>
                            <th class="px-4 py-3">Template Wajah</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($users as $usr)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white">{{ $usr->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $usr->email }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg border
                                        @if($usr->role === 'owner') bg-purple-500/20 text-purple-300 border-purple-500/30
                                        @elseif($usr->role === 'admin') bg-blue-500/20 text-blue-300 border-blue-500/30
                                        @elseif($usr->role === 'mentor') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                        @elseif($usr->role === 'karyawan') bg-amber-500/20 text-amber-300 border-amber-500/30
                                        @else bg-cyan-500/20 text-cyan-300 border-cyan-500/30 @endif">
                                        {{ $usr->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-300 font-mono">{{ $usr->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($usr->face_descriptor)
                                        <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1">
                                            <i class="ti ti-circle-check font-bold"></i> Terdaftar
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-rose-400 flex items-center gap-1">
                                            <i class="ti ti-alert-circle"></i> Belum Ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($usr->is_active)
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-500/20 text-emerald-300">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-500/20 text-rose-300">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('users.register-face', $usr->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 text-xs font-semibold rounded-lg border border-indigo-500/30">
                                        <i class="ti ti-scan"></i> Scan Wajah
                                    </a>
                                    <a href="{{ route('users.edit', $usr->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg border border-slate-700">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('users.destroy', $usr->id) }}" class="inline" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-500">Belum ada data user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
