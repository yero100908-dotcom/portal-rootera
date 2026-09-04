<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Order Book & Pesanan Klien</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manajemen order transaksi, rincian item, dan status pelunasan</p>
            </div>
            <a href="{{ route('orders.create') }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i class="ti ti-plus text-sm"></i> Input Order Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search & Filter Card -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 sm:p-5 shadow-sm transition-colors duration-200">
            <form method="GET" action="{{ route('orders.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Order, Nama Klien, atau rincian..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="w-full sm:w-48">
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Status Pembayaran --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="dp" {{ request('status') === 'dp' ? 'selected' : '' }}>DP (Uang Muka)</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                    <i class="ti ti-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-950 text-[11px] uppercase font-semibold text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
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
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                        @forelse($orders as $ord)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-4 py-3 font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $ord->order_number }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $ord->client_name }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5"><i class="ti ti-phone"></i> {{ $ord->client_phone ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300 max-w-xs truncate">{{ $ord->details }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $ord->transaction_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                        @if($ord->payment_status === 'lunas') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                        @elseif($ord->payment_status === 'dp') bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                        @elseif($ord->payment_status === 'pending') bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20
                                        @else bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20 @endif">
                                        {{ $ord->payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-1.5">
                                    <a href="{{ route('orders.edit', $ord->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-300 text-[11px] font-semibold rounded border border-slate-200 dark:border-slate-700 transition">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('orders.destroy', $ord->id) }}" class="inline" onsubmit="return confirm('Hapus order ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-[11px] font-semibold rounded border border-rose-500/20 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                    Belum ada data order terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
