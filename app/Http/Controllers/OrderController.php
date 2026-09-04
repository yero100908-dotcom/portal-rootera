<?php

namespace App\Http\Controllers;

use App\Exports\OrderRecapExport;
use App\Models\Cashflow;
use App\Models\Order;
use App\Models\User;
use App\Services\AuditLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['creator', 'pic']);

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
        $usersList = User::where('is_active', true)->get();

        return view('orders.index', compact('orders', 'usersList'));
    }

    public function recap(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $leadSource = $request->input('lead_source');

        $query = Order::with(['creator', 'pic'])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if (! empty($leadSource)) {
            $query->where('lead_source', $leadSource);
        }

        $allFiltered = (clone $query)->get();
        $totalCount = $allFiltered->count();
        $totalOmset = $allFiltered->sum('total_amount');
        $totalCommission = $allFiltered->sum('commission_amount');

        $orders = $query->orderBy('transaction_date', 'desc')->paginate(20)->withQueryString();
        $leadSourcesList = ['WhatsApp', 'Instagram', 'TikTok', 'Website', 'Referensi', 'Direct'];
        $usersList = User::where('is_active', true)->get();

        return view('orders.recap', compact(
            'orders',
            'month',
            'year',
            'leadSource',
            'totalCount',
            'totalOmset',
            'totalCommission',
            'leadSourcesList',
            'usersList'
        ));
    }

    public function exportRecap(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $leadSource = $request->input('lead_source');

        $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
        $fileName = "order-rekap-{$monthPadded}-{$year}.xlsx";

        AuditLoggerService::log('Export Rekap Order', "Ekspor file excel rekap order periode {$monthPadded}/{$year}");

        return Excel::download(new OrderRecapExport($month, $year, $leadSource), $fileName);
    }

    public function create()
    {
        $usersList = User::where('is_active', true)->get();

        return view('orders.create', compact('usersList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'lead_source' => 'nullable|string|max:100',
            'details' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_status' => 'required|in:pending,dp,lunas,cancelled',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        $orderNumber = 'ORD-'.date('Ymd').'-'.rand(1000, 9999);

        $totalAmount = (float) $request->total_amount;
        $commRate = (float) ($request->commission_rate ?? 0);
        $commAmount = $request->filled('commission_amount')
            ? (float) $request->commission_amount
            : ($totalAmount * ($commRate / 100));

        $order = Order::create([
            'order_number' => $orderNumber,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'lead_source' => $request->lead_source,
            'details' => $request->details,
            'total_amount' => $totalAmount,
            'commission_rate' => $commRate,
            'commission_amount' => $commAmount,
            'transaction_date' => $request->transaction_date,
            'payment_status' => $request->payment_status,
            'created_by' => Auth::id(),
            'pic_user_id' => $request->pic_user_id,
        ]);

        if (in_array($request->payment_status, ['lunas', 'dp'])) {
            Cashflow::create([
                'type' => 'inflow',
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'date' => $order->transaction_date,
                'source_destination' => "Pelunasan Order {$order->order_number} ({$order->client_name})",
                'category' => 'Penjualan / Order Client',
                'created_by' => Auth::id(),
                'note' => 'Otomatis dicatat dari Order #'.$order->order_number,
            ]);
        }

        AuditLoggerService::log('Tambah Order', "Order baru #{$order->order_number} sebesar Rp ".number_format($order->total_amount, 0, ',', '.'));

        return redirect()->route('orders.index')->with('success', 'Order baru berhasil ditambahkan.');
    }

    public function edit(Order $order)
    {
        $usersList = User::where('is_active', true)->get();

        return view('orders.edit', compact('order', 'usersList'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'lead_source' => 'nullable|string|max:100',
            'details' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_amount' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_status' => 'required|in:pending,dp,lunas,cancelled',
            'pic_user_id' => 'nullable|exists:users,id',
        ]);

        $oldStatus = $order->payment_status;
        $totalAmount = (float) $request->total_amount;
        $commRate = (float) ($request->commission_rate ?? 0);
        $commAmount = $request->filled('commission_amount')
            ? (float) $request->commission_amount
            : ($totalAmount * ($commRate / 100));

        $order->update([
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'lead_source' => $request->lead_source,
            'details' => $request->details,
            'total_amount' => $totalAmount,
            'commission_rate' => $commRate,
            'commission_amount' => $commAmount,
            'transaction_date' => $request->transaction_date,
            'payment_status' => $request->payment_status,
            'pic_user_id' => $request->pic_user_id,
        ]);

        if (! in_array($oldStatus, ['lunas', 'dp']) && in_array($request->payment_status, ['lunas', 'dp'])) {
            Cashflow::create([
                'type' => 'inflow',
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'date' => $order->transaction_date,
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
