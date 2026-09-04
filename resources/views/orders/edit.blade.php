<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
            <div>
                <h1 class="font-bold text-xl text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                    <i class="ti ti-edit text-indigo-600 dark:text-indigo-400"></i> Edit Order #{{ $order->order_number }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Perbarui data order atau status pelunasan pembayaran</p>
            </div>
            <div>
                <a href="{{ route('orders.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg text-xs transition border border-slate-200 dark:border-slate-700">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm transition-colors duration-200">
        <form method="POST" action="{{ route('orders.update', $order->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Nama Klien / Pelanggan *</label>
                <input type="text" name="client_name" required value="{{ old('client_name', $order->client_name) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">No WhatsApp / Telepon Klien</label>
                <input type="text" name="client_phone" value="{{ old('client_phone', $order->client_phone) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Rincian Barang / Jasa *</label>
                <textarea name="details" required rows="4" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">{{ old('details', $order->details) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Total Nominal (Rp) *</label>
                    <input type="number" step="0.01" name="total_amount" required value="{{ old('total_amount', $order->total_amount) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Tanggal Transaksi *</label>
                    <input type="date" name="transaction_date" required value="{{ old('transaction_date', $order->transaction_date->format('Y-m-d')) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Status Pembayaran *</label>
                <select name="payment_status" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-lg px-3.5 py-2 text-xs focus:ring-1 focus:ring-indigo-500">
                    <option value="pending" {{ old('payment_status', $order->payment_status) === 'pending' ? 'selected' : '' }}>Pending (Belum Bayar)</option>
                    <option value="dp" {{ old('payment_status', $order->payment_status) === 'dp' ? 'selected' : '' }}>DP (Uang Muka)</option>
                    <option value="lunas" {{ old('payment_status', $order->payment_status) === 'lunas' ? 'selected' : '' }}>Lunas (Tercatat Otomatis ke Arus Kas)</option>
                    <option value="cancelled" {{ old('payment_status', $order->payment_status) === 'cancelled' ? 'selected' : '' }}>Batal (Cancelled)</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg text-xs border border-slate-200 dark:border-slate-700 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg text-xs shadow-sm transition">
                    Perbarui Order
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
