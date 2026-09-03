<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-wallet text-indigo-400"></i> Modul Pembukuan & Manajemen Arus Kas (Cashflow)
            </h2>
            <p class="text-sm text-slate-400 mt-1">Pencatatan uang masuk, uang keluar, upload bukti transfer, dan filter saldo</p>
        </div>
        <div class="flex items-center gap-3" x-data="{ openInflow: false, openOutflow: false }">
            <button @click="$dispatch('open-inflow-modal')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                <i class="ti ti-plus text-lg"></i> Uang Masuk (Inflow)
            </button>
            <button @click="$dispatch('open-outflow-modal')" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-rose-600/30 transition flex items-center gap-2">
                <i class="ti ti-minus text-lg"></i> Uang Keluar (Outflow)
            </button>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="{ showInflowModal: false, showOutflowModal: false }">
        
        <!-- Financial Summary Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Uang Masuk (Inflow)</p>
                <h3 class="text-3xl font-extrabold text-emerald-400 mt-1">Rp {{ number_format($summary['total_inflow'], 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-2">Termasuk pelunasan order & modal</p>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Uang Keluar (Outflow)</p>
                <h3 class="text-3xl font-extrabold text-rose-400 mt-1">Rp {{ number_format($summary['total_outflow'], 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-2">Gaji mentor, server & operasional</p>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Saldo / Laba Bersih Saat Ini</p>
                <h3 class="text-3xl font-extrabold text-purple-300 mt-1">Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-2">Total Inflow - Outflow</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <form method="GET" action="{{ route('finance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tipe Arus Kas</label>
                    <select name="type" class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Tipe --</option>
                        <option value="inflow" {{ request('type') === 'inflow' ? 'selected' : '' }}>Uang Masuk (Inflow)</option>
                        <option value="outflow" {{ request('type') === 'outflow' ? 'selected' : '' }}>Uang Keluar (Outflow)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kategori</label>
                    <input type="text" name="category" value="{{ request('category') }}" placeholder="Cari kategori..." class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl transition shadow">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    <a href="{{ route('finance.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-xl border border-slate-700">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Cashflow Transactions Table -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl overflow-hidden">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="ti ti-list text-indigo-400"></i> Riwayat Transaksi Arus Kas
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Sumber / Tujuan</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3">Bukti Struk</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($cashflows as $cf)
                            <tr class="hover:bg-slate-800/40">
                                <td class="px-4 py-3 text-xs text-slate-400 font-semibold">{{ $cf->date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-lg border
                                        @if($cf->type === 'inflow') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                        @else bg-rose-500/20 text-rose-300 border-rose-500/30 @endif">
                                        {{ $cf->type === 'inflow' ? 'Uang Masuk' : 'Uang Keluar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-white">
                                    {{ $cf->source_destination }}
                                    @if($cf->account_number)
                                        <div class="text-xs font-normal text-slate-400 font-mono mt-0.5">Rek: {{ $cf->account_number }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-indigo-300 font-medium">{{ $cf->category }}</td>
                                <td class="px-4 py-3 font-extrabold text-base {{ $cf->type === 'inflow' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $cf->type === 'inflow' ? '+' : '-' }} Rp {{ number_format($cf->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($cf->proof_file)
                                        <a href="{{ asset($cf->proof_file) }}" target="_blank" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-semibold rounded-lg border border-slate-700 inline-flex items-center gap-1">
                                            <i class="ti ti-file-text"></i> Lihat Struk
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-500 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('finance.destroy', $cf->id) }}" class="inline" onsubmit="return confirm('Hapus entri transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold rounded-lg border border-rose-500/30">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-500">
                                    Belum ada data transaksi arus kas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $cashflows->links() }}
            </div>
        </div>

        <!-- Modal Inflow (Uang Masuk) -->
        <div x-show="showInflowModal" @open-inflow-modal.window="showInflowModal = true" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur flex items-center justify-center p-4" x-cloak>
            <div class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="ti ti-arrow-up-right text-emerald-400"></i> Catat Uang Masuk (Inflow)
                    </h3>
                    <button @click="showInflowModal = false" class="text-slate-400 hover:text-white"><i class="ti ti-x text-xl"></i></button>
                </div>

                <form method="POST" action="{{ route('finance.inflow.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nominal (Rp) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="5000000" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Penerimaan *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Sumber Uang Masuk *</label>
                        <input type="text" name="source_destination" required placeholder="Contoh: Pelunasan Order #123 / Modal Awal" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kategori Uang Masuk *</label>
                        <select name="category" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach($inflowCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor Rekening Penampung</label>
                        <input type="text" name="account_number" placeholder="Contoh: BCA 1234567890" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Catatan Tambahan</label>
                        <textarea name="note" rows="2" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="showInflowModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 font-semibold rounded-xl text-sm border border-slate-700">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm shadow">Simpan Uang Masuk</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Outflow (Uang Keluar) -->
        <div x-show="showOutflowModal" @open-outflow-modal.window="showOutflowModal = true" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur flex items-center justify-center p-4" x-cloak>
            <div class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="ti ti-arrow-down-left text-rose-400"></i> Catat Uang Keluar (Outflow)
                    </h3>
                    <button @click="showOutflowModal = false" class="text-slate-400 hover:text-white"><i class="ti ti-x text-xl"></i></button>
                </div>

                <form method="POST" action="{{ route('finance.outflow.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nominal Pengeluaran (Rp) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="1500000" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Pengeluaran *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tujuan / Peruntukan Pengeluaran *</label>
                        <input type="text" name="source_destination" required placeholder="Contoh: Gaji Mentor Budi / Biaya Server AWS" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kategori Pengeluaran *</label>
                        <select name="category" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                            @foreach($outflowCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Upload Bukti Pembayaran / Struk (PDF/Gambar)</label>
                        <input type="file" name="proof_file" accept="image/*,.pdf" class="w-full bg-slate-950 border border-slate-700 text-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Catatan Tambahan</label>
                        <textarea name="note" rows="2" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="showOutflowModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 font-semibold rounded-xl text-sm border border-slate-700">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-sm shadow">Simpan Uang Keluar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
