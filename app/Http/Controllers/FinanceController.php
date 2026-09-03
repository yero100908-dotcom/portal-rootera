<?php

namespace App\Http\Controllers;

use App\Models\Cashflow;
use App\Models\Order;
use App\Services\AuditLoggerService;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FinanceController extends Controller
{
    protected FinancialService $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $category = $request->get('category');
        $type = $request->get('type');

        $summary = $this->financialService->getFinancialSummary($startDate, $endDate, $category);

        $query = Cashflow::with('order', 'creator');

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($type) {
            $query->where('type', $type);
        }

        $cashflows = $query->latest('date')->latest('id')->paginate(15)->withQueryString();

        $inflowCategories = ['Penjualan / Order Client', 'Modal', 'Sponsorship', 'Donasi', 'Pendapatan Lain-lain'];
        $outflowCategories = ['Gaji Mentor / Staff', 'Biaya Server & Cloud', 'Operasional Kantor', 'Iklan & Marketing', 'Peralatan & Asset', 'Lain-lain'];

        return view('finance.index', compact('summary', 'cashflows', 'inflowCategories', 'outflowCategories'));
    }

    public function storeInflow(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'source_destination' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $cashflow = Cashflow::create([
            'type' => 'inflow',
            'amount' => $request->amount,
            'date' => $request->date,
            'source_destination' => $request->source_destination,
            'category' => $request->category,
            'account_number' => $request->account_number,
            'note' => $request->note,
            'created_by' => Auth::id(),
        ]);

        AuditLoggerService::log('Input Uang Masuk', "Catat pemasukan Rp " . number_format($request->amount, 0, ',', '.') . " dari {$request->source_destination}");

        return redirect()->route('finance.index')->with('success', 'Uang masuk berhasil dicatat.');
    }

    public function storeOutflow(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'source_destination' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'proof_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096',
            'note' => 'nullable|string',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('receipts', 'public');
            $proofPath = 'storage/' . $path;
        }

        $cashflow = Cashflow::create([
            'type' => 'outflow',
            'amount' => $request->amount,
            'date' => $request->date,
            'source_destination' => $request->source_destination,
            'category' => $request->category,
            'proof_file' => $proofPath,
            'note' => $request->note,
            'created_by' => Auth::id(),
        ]);

        AuditLoggerService::log('Input Uang Keluar', "Catat pengeluaran Rp " . number_format($request->amount, 0, ',', '.') . " untuk {$request->source_destination}");

        return redirect()->route('finance.index')->with('success', 'Uang keluar berhasil dicatat.');
    }

    public function destroy(Cashflow $cashflow)
    {
        AuditLoggerService::log('Hapus Transaksi', "Menghapus data transaksi #{$cashflow->id} ({$cashflow->type})");

        if ($cashflow->proof_file && str_contains($cashflow->proof_file, 'storage/')) {
            $relativePath = str_replace('storage/', '', $cashflow->proof_file);
            Storage::disk('public')->delete($relativePath);
        }

        $cashflow->delete();

        return redirect()->route('finance.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function profitLossReport(Request $request)
    {
        // Reserved for Owner
        if (! Auth::user()->isOwner()) {
            abort(403, 'Halaman Laba Rugi khusus untuk Owner.');
        }

        $year = $request->get('year', date('Y'));

        $monthlyInflow = Cashflow::where('type', 'inflow')
            ->whereYear('date', $year)
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyOutflow = Cashflow::where('type', 'outflow')
            ->whereYear('date', $year)
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return view('finance.profit_loss', compact('year', 'monthlyInflow', 'monthlyOutflow'));
    }
}
