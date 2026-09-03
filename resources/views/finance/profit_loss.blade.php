<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Laporan Laba Rugi Perusahaan</h1>
                <p class="text-xs text-slate-400 mt-0.5">Summary bulanan & margin keuntungan finansial tahun {{ $year }}</p>
            </div>
            <form method="GET" action="{{ route('finance.profit_loss') }}" class="flex items-center gap-2">
                <select name="year" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 text-white rounded-lg px-3 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Monthly Profit & Loss Table -->
        <div class="bg-slate-900/60 border border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i class="ti ti-table text-indigo-400"></i> Breakdown Bulanan Tahun {{ $year }}
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-[11px] uppercase font-semibold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Bulan</th>
                            <th class="px-4 py-3 text-right">Pemasukan (Inflow)</th>
                            <th class="px-4 py-3 text-right">Pengeluaran (Outflow)</th>
                            <th class="px-4 py-3 text-right">Laba / (Rugi) Bersih</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            $grandInflow = 0;
                            $grandOutflow = 0;
                        @endphp

                        @foreach($months as $num => $name)
                            @php
                                $in = $monthlyInflow[$num] ?? 0;
                                $out = $monthlyOutflow[$num] ?? 0;
                                $profit = $in - $out;
                                $grandInflow += $in;
                                $grandOutflow += $out;
                            @endphp
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-4 py-3 font-semibold text-white">{{ $name }}</td>
                                <td class="px-4 py-3 text-right font-medium text-emerald-400">Rp {{ number_format($in, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-medium text-rose-400">Rp {{ number_format($out, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold {{ $profit >= 0 ? 'text-indigo-300' : 'text-rose-400' }}">
                                    Rp {{ number_format($profit, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($profit > 0)
                                        <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border bg-emerald-500/10 text-emerald-400 border-emerald-500/20">Profit</span>
                                    @elseif($profit < 0)
                                        <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border bg-rose-500/10 text-rose-400 border-rose-500/20">Defisit</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border bg-slate-800 text-slate-400 border-slate-700">Impas</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-950 text-white font-bold border-t border-slate-800">
                        <tr>
                            <td class="px-4 py-3 uppercase tracking-wider text-[11px]">TOTAL TAHUN {{ $year }}</td>
                            <td class="px-4 py-3 text-right text-emerald-400">Rp {{ number_format($grandInflow, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-400">Rp {{ number_format($grandOutflow, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-indigo-300 text-sm">Rp {{ number_format($grandInflow - $grandOutflow, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center text-[10px] uppercase text-slate-400">Audited</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
