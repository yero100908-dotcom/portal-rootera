<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-chart-dots text-purple-400"></i> Laporan Laba Rugi Perusahaan (Owner Exclusive)
            </h2>
            <p class="text-sm text-slate-400 mt-1">Ringkasan finansial bulanan, margin keuntungan, dan performa keuangan tahun {{ $year }}</p>
        </div>
        <div>
            <form method="GET" action="{{ route('finance.profit_loss') }}" class="flex items-center gap-2">
                <select name="year" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-2 text-sm">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Monthly Profit & Loss Table Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <i class="ti ti-table text-indigo-400"></i> Breakdown Laba / Rugi Bulanan Tahun {{ $year }}
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Bulan</th>
                            <th class="px-4 py-3 text-right">Pemasukan (Inflow)</th>
                            <th class="px-4 py-3 text-right">Pengeluaran (Outflow)</th>
                            <th class="px-4 py-3 text-right">Laba / (Rugi) Bersih</th>
                            <th class="px-4 py-3 text-center">Status Financial</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
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
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3.5 font-bold text-white">{{ $name }}</td>
                                <td class="px-4 py-3.5 text-right font-semibold text-emerald-400">Rp {{ number_format($in, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5 text-right font-semibold text-rose-400">Rp {{ number_format($out, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5 text-right font-extrabold {{ $profit >= 0 ? 'text-purple-300' : 'text-rose-500' }}">
                                    Rp {{ number_format($profit, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($profit > 0)
                                        <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Surplus / Profit</span>
                                    @elseif($profit < 0)
                                        <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/30">Defisit / Deficit</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg bg-slate-800 text-slate-400 border border-slate-700">Impas / Flat</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-950 text-white font-extrabold border-t-2 border-indigo-500/50">
                        <tr>
                            <td class="px-4 py-4 uppercase tracking-wider text-xs">TOTAL TAHUN {{ $year }}</td>
                            <td class="px-4 py-4 text-right text-emerald-400">Rp {{ number_format($grandInflow, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-rose-400">Rp {{ number_format($grandOutflow, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-purple-300 text-base">Rp {{ number_format($grandInflow - $grandOutflow, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center text-xs uppercase text-indigo-400">Owner Audit Checked</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
