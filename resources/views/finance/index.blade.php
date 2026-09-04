<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Pembukuan & Arus Kas (Cashflow)</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan real-time arus uang masuk, pengeluaran operasional, dan bukti struk</p>
            </div>
            <div class="flex items-center gap-2" x-data="{ openInflow: false, openOutflow: false }">
                <button @click="$dispatch('open-inflow-modal')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs rounded-lg shadow-sm transition flex items-center gap-1">
                    <i class="ti ti-plus"></i> Uang Masuk (Inflow)
                </button>
                <button @click="$dispatch('open-outflow-modal')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white font-medium text-xs rounded-lg shadow-sm transition flex items-center gap-1">
                    <i class="ti ti-minus"></i> Uang Keluar (Outflow)
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ showInflowModal: false, showOutflowModal: false }">
        
        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm transition-colors duration-200">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Uang Masuk</span>
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 tracking-tight">Rp {{ number_format($summary['total_inflow'], 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Order lunas & modal masuk</p>
            </div>

            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm transition-colors duration-200">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Pengeluaran</span>
                <h3 class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1 tracking-tight">Rp {{ number_format($summary['total_outflow'], 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Gaji mentor & operasional</p>
            </div>

            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm transition-colors duration-200">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Saldo / Net Profit</span>
                <h3 class="text-2xl font-bold text-indigo-600 dark:text-indigo-300 mt-1 tracking-tight">Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Selisih arus kas bersih</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-4 sm:p-5 shadow-sm transition-colors duration-200">
            <form method="GET" action="{{ route('finance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Tipe</label>
                    <select name="type" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                        <option value="">-- Semua Tipe --</option>
                        <option value="inflow" {{ request('type') === 'inflow' ? 'selected' : '' }}>Uang Masuk</option>
                        <option value="outflow" {{ request('type') === 'outflow' ? 'selected' : '' }}>Uang Keluar</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Kategori</label>
                    <input type="text" name="category" value="{{ request('category') }}" placeholder="Kategori..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-200 text-xs rounded-lg px-3 py-2 focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    <a href="{{ route('finance.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-xl p-5 shadow-sm overflow-hidden transition-colors duration-200">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="ti ti-list text-indigo-600 dark:text-indigo-400"></i> Riwayat Transaksi Cashflow
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-950 text-[11px] uppercase font-semibold text-slate-600 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Sumber / Peruntukan</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3">Struk</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                        @forelse($cashflows as $cf)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-mono">{{ $cf->date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border
                                        @if($cf->type === 'inflow') bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20
                                        @else bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/20 @endif">
                                        {{ $cf->type === 'inflow' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                                    {{ $cf->source_destination }}
                                    @if($cf->account_number)
                                        <div class="text-[11px] font-normal text-slate-500 dark:text-slate-400 font-mono mt-0.5">Rek: {{ $cf->account_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-indigo-600 dark:text-indigo-400 font-medium">{{ $cf->category }}</td>
                                <td class="px-4 py-3 font-bold text-sm {{ $cf->type === 'inflow' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $cf->type === 'inflow' ? '+' : '-' }} Rp {{ number_format($cf->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($cf->proof_file)
                                        <a href="{{ asset($cf->proof_file) }}" target="_blank" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-300 text-[11px] font-semibold rounded border border-slate-200 dark:border-slate-700 inline-flex items-center gap-1 transition">
                                            <i class="ti ti-file-text"></i> Struk
                                        </a>
                                    @else
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('finance.destroy', $cf->id) }}" class="inline" onsubmit="return confirm('Hapus entri transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-[11px] font-semibold rounded border border-rose-500/20 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                    Belum ada transaksi cashflow.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $cashflows->links() }}
            </div>
        </div>

        <!-- Modal Inflow (Uang Masuk) -->
        <div x-show="showInflowModal" @open-inflow-modal.window="showInflowModal = true" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="showInflowModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-xl p-6 shadow-2xl space-y-4 transition-colors duration-200">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ti ti-arrow-up-right text-emerald-600 dark:text-emerald-400"></i> Catat Uang Masuk (Inflow)
                    </h3>
                    <button @click="showInflowModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="ti ti-x text-lg"></i></button>
                </div>

                <form method="POST" action="{{ route('finance.inflow.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Nominal (Rp) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="5000000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Tanggal *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Sumber Uang Masuk *</label>
                        <input type="text" name="source_destination" required placeholder="Contoh: Pelunasan Client / Modal" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Kategori *</label>
                        <select name="category" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                            @foreach($inflowCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button type="button" @click="showInflowModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg shadow-sm">Simpan Uang Masuk</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Outflow (Uang Keluar) -->
        <div x-show="showOutflowModal" @open-outflow-modal.window="showOutflowModal = true" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
            <div @click.away="showOutflowModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 w-full max-w-lg rounded-xl p-6 shadow-2xl space-y-4 transition-colors duration-200">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="ti ti-arrow-down-left text-rose-600 dark:text-rose-400"></i> Catat Uang Keluar (Outflow)
                    </h3>
                    <button @click="showOutflowModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="ti ti-x text-lg"></i></button>
                </div>

                <form method="POST" action="{{ route('finance.outflow.store') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Nominal (Rp) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="1500000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Tanggal *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Peruntukan Pengeluaran *</label>
                        <input type="text" name="source_destination" required placeholder="Contoh: Gaji Mentor / Biaya Server" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Kategori *</label>
                        <select name="category" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                            @foreach($outflowCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">Upload Bukti Struk (PDF/Gambar)</label>
                        <input type="file" name="proof_file" accept="image/*,.pdf" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-lg px-2.5 py-1.5 text-xs">
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button type="button" @click="showOutflowModal = false" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-sm">Simpan Uang Keluar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
