<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\Order;
use App\Services\AuditLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('creator');

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'details' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_status' => 'required|in:pending,dp,lunas,cancelled',
        ]);

        $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        $order = Order::create([
            'order_number' => $orderNumber,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'details' => $request->details,
            'total_amount' => $request->total_amount,
            'transaction_date' => $request->transaction_date,
            'payment_status' => $request->payment_status,
            'created_by' => Auth::id(),
        ]);

        // Auto create inflow cashflow record if paid or DP
        if (in_array($request->payment_status, ['lunas', 'dp'])) {
            Cashflow::create([
                'type' => 'inflow',
                'order_id' => $order->id,
                'amount' => $request->total_amount,
                'date' => $request->transaction_date,
                'source_destination' => "Pelunasan Order {$order->order_number} ({$order->client_name})",
                'category' => 'Penjualan / Order Client',
                'created_by' => Auth::id(),
                'note' => 'Otomatis dicatat dari Order #' . $order->order_number,
            ]);
        }

        AuditLoggerService::log('Tambah Order', "Order baru #{$order->order_number} sebesar Rp " . number_format($order->total_amount, 0, ',', '.'));

        return redirect()->route('orders.index')->with('success', 'Order baru berhasil ditambahkan.');
    }

    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'details' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_status' => 'required|in:pending,dp,lunas,cancelled',
        ]);

        $oldStatus = $order->payment_status;

        $order->update([
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'details' => $request->details,
            'total_amount' => $request->total_amount,
            'transaction_date' => $request->transaction_date,
            'payment_status' => $request->payment_status,
        ]);

        // Sync with Cashflow if updated to paid
        if (! in_array($oldStatus, ['lunas', 'dp']) && in_array($request->payment_status, ['lunas', 'dp'])) {
            Cashflow::create([
                'type' => 'inflow',
                'order_id' => $order->id,
                'amount' => $request->total_amount,
                'date' => $request->transaction_date,
                'source_destination' => "Pembayaran Order {$order->order_number} ({$order->client_name})",
                'category' => 'Penjualan / Order Client',
                'created_by' => Auth::id(),
                'note' => 'Dicatat otomatis dari pembaruan status order.',
            ]);
        }

        AuditLoggerService::log('Update Order', "Memperbarui data order #{$order->order_number}");

        return redirect()->route('orders.index')->with('success', 'Order berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        AuditLoggerService::log('Hapus Order', "Menghapus order #{$order->order_number}");
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus.');
    }
}
