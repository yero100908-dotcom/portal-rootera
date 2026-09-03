<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-shopping-cart text-indigo-400"></i> Modul Order & Manajemen Pesanan Klien
            </h2>
            <p class="text-sm text-slate-400 mt-1">Pencatatan order baru, status pelunasan, dan otomasisasi arus kas masuk</p>
        </div>
        <div>
            <a href="{{ route('orders.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <i class="ti ti-plus text-lg"></i> Input Order Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter & Search Bar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <form method="GET" action="{{ route('orders.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Order, Nama Klien, atau Detail Pesanan..." class="w-full bg-slate-950 border border-slate-700 text-slate-200 text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="w-full md:w-48">
                    <select name="status" class="w-full bg-slate-950 border border-slate-700 text-slate-200 text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Status Pembayaran --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="dp" {{ request('status') === 'dp' ? 'selected' : '' }}>DP (Uang Muka)</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl transition shadow">
                    <i class="ti ti-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Orders Table Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">No Order</th>
                            <th class="px-4 py-3">Klien & Kontak</th>
                            <th class="px-4 py-3">Rincian Order</th>
                            <th class="px-4 py-3">Total Nominal</th>
                            <th class="px-4 py-3">Tgl Transaksi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($orders as $ord)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3 font-mono font-bold text-indigo-300">{{ $ord->order_number }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-white">{{ $ord->client_name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5"><i class="ti ti-phone"></i> {{ $ord->client_phone ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-300 max-w-xs truncate">{{ $ord->details }}</td>
                                <td class="px-4 py-3 font-extrabold text-white">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-xs text-slate-400">{{ $ord->transaction_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg border
                                        @if($ord->payment_status === 'lunas') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                        @elseif($ord->payment_status === 'dp') bg-amber-500/20 text-amber-300 border-amber-500/30
                                        @elseif($ord->payment_status === 'pending') bg-blue-500/20 text-blue-300 border-blue-500/30
                                        @else bg-rose-500/20 text-rose-300 border-rose-500/30 @endif">
                                        {{ $ord->payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('orders.edit', $ord->id) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-semibold rounded-lg border border-slate-700">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('orders.destroy', $ord->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus order ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30">
                                            <i class="ti ti-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-500">
                                    Belum ada data order terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
