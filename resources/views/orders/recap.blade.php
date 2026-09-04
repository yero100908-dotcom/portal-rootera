<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="ti ti-file-analytics text-indigo-600 dark:text-indigo-400"></i> Rekap Order Bulanan & Komisi
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Rincian transaksi harian, analisis sumber pesanan, dan perhitungan komisi sales</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('orders.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg border border-slate-200 dark:border-slate-700 transition">
                    &larr; Order Book Utama
                </a>
                <a href="{{ route('orders.recap.export', ['month' => $month, 'year' => $year, 'lead_source' => $leadSource]) }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-lg shadow-sm transition flex items-center gap-1.5">
                    <i class="ti ti-file-spreadsheet text-base"></i> Ekspor Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 sm:p-5 shadow-sm transition-colors duration-200">
            <form method="GET" action="{{ route('orders.recap') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Pilih Bulan</label>
                    <select name="month" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        @php
                            $monthsList = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                        @endphp
                        @foreach($monthsList as $num => $name)
                            <option value="{{ $num }}" {{ sprintf('%02d', $month) == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Pilih Tahun</label>
                    <select name="year" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Asal Pemesan (Lead Source)</label>
                    <select name="lead_source" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Semua Sumber --</option>
                        @foreach($leadSourcesList as $src)
                            <option value="{{ $src }}" {{ $leadSource == $src ? 'selected' : '' }}>{{ $src }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-sm flex items-center justify-center gap-1.5">
                        <i class="ti ti-filter"></i> Filter Data
                    </button>
                    @if(request()->hasAny(['month', 'year', 'lead_source']))
                        <a href="{{ route('orders.recap') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 text-xs font-semibold rounded-lg transition border border-slate-200 dark:border-slate-700" title="Reset Filter">
                            <i class="ti ti-rotate-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Order Periode Ini</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                        <i class="ti ti-shopping-cart text-lg"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ number_format($totalCount, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">Transaksi</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">Jumlah pesanan terdaftar</p>
            </div>

            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Omset Order</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                        <i class="ti ti-wallet text-lg"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">Akumulasi nilai transaksi order</p>
            </div>

            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Komisi Diterima</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20">
                        <i class="ti ti-gift text-lg"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                    Rp {{ number_format($totalCommission, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">Total komisi untuk sales / tim</p>
            </div>
        </div>

        <!-- Detailed Recap Table -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="ti ti-list text-indigo-600 dark:text-indigo-400"></i> Data Rincian Order & Komisi
                </h3>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Periode: {{ $monthsList[sprintf('%02d', $month)] ?? $month }} {{ $year }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-950 text-[11px] uppercase font-semibold text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-3 py-3 text-center">No</th>
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3">No Order</th>
                            <th class="px-3 py-3">Nama Pemesan</th>
                            <th class="px-3 py-3 text-center">Asal Pemesan</th>
                            <th class="px-3 py-3">Produk / Layanan</th>
                            <th class="px-3 py-3 text-right">Nilai Transaksi (Rp)</th>
                            <th class="px-3 py-3 text-right">Komisi (Rp)</th>
                            <th class="px-3 py-3 text-center">Status</th>
                            <th class="px-3 py-3">PIC / Sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                        @forelse($orders as $index => $ord)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-3 py-3 text-center font-mono text-slate-500">{{ $orders->firstItem() + $index }}</td>
                                <td class="px-3 py-3 font-medium text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $ord->transaction_date->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-3 font-mono font-semibold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                    {{ $ord->order_number }}
                                </td>
                                <td class="px-3 py-3 font-bold text-slate-900 dark:text-white">
                                    {{ $ord->client_name }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full border
                                        @if($ord->lead_source === 'WhatsApp') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                        @elseif($ord->lead_source === 'Instagram') bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/20
                                        @elseif($ord->lead_source === 'TikTok') bg-slate-800 text-white dark:bg-slate-700 border-slate-600
                                        @elseif($ord->lead_source === 'Website') bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20
                                        @elseif($ord->lead_source === 'Referensi') bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                        @else bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700 @endif">
                                        {{ $ord->lead_source ?? 'Direct' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $ord->details }}">
                                    {{ $ord->details }}
                                </td>
                                <td class="px-3 py-3 text-right font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                    Rp {{ number_format($ord->commission_amount, 0, ',', '.') }}
                                    @if($ord->commission_rate > 0)
                                        <span class="block text-[10px] text-slate-400 font-normal">({{ number_format($ord->commission_rate, 1) }}%)</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                        @if($ord->payment_status === 'lunas') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                        @elseif($ord->payment_status === 'dp') bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20
                                        @elseif($ord->payment_status === 'pending') bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20
                                        @else bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20 @endif">
                                        {{ $ord->payment_status }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                    {{ $ord->pic->name ?? ($ord->creator->name ?? '-') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-8 text-slate-400 dark:text-slate-500 text-xs">
                                    Belum ada transaksi order tercatat pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($orders->count() > 0)
                        <tfoot class="bg-slate-100 dark:bg-slate-950/80 font-bold border-t border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white">
                            <tr>
                                <td colspan="6" class="px-3 py-3 text-right text-xs uppercase tracking-wider">
                                    Total Akumulasi Periode Ini:
                                </td>
                                <td class="px-3 py-3 text-right text-xs font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3 text-right text-xs font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                    Rp {{ number_format($totalCommission, 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
