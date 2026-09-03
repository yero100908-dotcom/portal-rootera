<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <i class="ti ti-edit text-indigo-400"></i> Edit Order #{{ $order->order_number }}
            </h2>
            <p class="text-sm text-slate-400 mt-1">Perbarui data order atau status pelunasan pembayaran</p>
        </div>
        <div>
            <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-sm transition border border-slate-700">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        <form method="POST" action="{{ route('orders.update', $order->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Klien / Pelanggan *</label>
                <input type="text" name="client_name" required value="{{ old('client_name', $order->client_name) }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">No WhatsApp / Telepon Klien</label>
                <input type="text" name="client_phone" value="{{ old('client_phone', $order->client_phone) }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Rincian Barang / Jasa *</label>
                <textarea name="details" required rows="4" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('details', $order->details) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Total Nominal (Rp) *</label>
                    <input type="number" step="0.01" name="total_amount" required value="{{ old('total_amount', $order->total_amount) }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Transaksi *</label>
                    <input type="date" name="transaction_date" required value="{{ old('transaction_date', $order->transaction_date->format('Y-m-d')) }}" class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Pembayaran *</label>
                <select name="payment_status" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="pending" {{ old('payment_status', $order->payment_status) === 'pending' ? 'selected' : '' }}>Pending (Belum Bayar)</option>
                    <option value="dp" {{ old('payment_status', $order->payment_status) === 'dp' ? 'selected' : '' }}>DP (Uang Muka)</option>
                    <option value="lunas" {{ old('payment_status', $order->payment_status) === 'lunas' ? 'selected' : '' }}>Lunas (Tercatat Otomatis ke Arus Kas)</option>
                    <option value="cancelled" {{ old('payment_status', $order->payment_status) === 'cancelled' ? 'selected' : '' }}>Batal (Cancelled)</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('orders.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-sm border border-slate-700">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-indigo-600/30 transition">
                    Perbarui Order
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
