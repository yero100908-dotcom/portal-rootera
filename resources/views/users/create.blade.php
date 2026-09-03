<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-user-plus text-indigo-400"></i> Tambah User Baru
            </h2>
            <p class="text-sm text-slate-400 mt-1">Daftarkan pengguna baru dan berikan salah satu dari 4 tingkatan hak akses</p>
        </div>
        <div>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-sm transition border border-slate-700">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Rian Pratama" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="rian@domain.com" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Password *</label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Role & Hak Akses *</label>
                    <select name="role" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ strtoupper($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">No Telepon / WA</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-800 text-slate-300 font-semibold rounded-xl text-sm border border-slate-700">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-indigo-600/30 transition">Simpan User</button>
            </div>
        </form>
    </div>
</x-app-layout>
