<?php

namespace App\Services;

use App\Models\Cashflow;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialService
{
    public function getFinancialSummary(?string $startDate = null, ?string $endDate = null, ?string $category = null): array
    {
        $query = Cashflow::query();

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $cashflows = $query->get();

        $totalInflow = $cashflows->where('type', 'inflow')->sum('amount');
        $totalOutflow = $cashflows->where('type', 'outflow')->sum('amount');
        $netProfit = $totalInflow - $totalOutflow;

        $inflowByCategory = $cashflows->where('type', 'inflow')->groupBy('category')->map(fn ($items) => $items->sum('amount'));
        $outflowByCategory = $cashflows->where('type', 'outflow')->groupBy('category')->map(fn ($items) => $items->sum('amount'));

        return [
            'total_inflow' => $totalInflow,
            'total_outflow' => $totalOutflow,
            'net_profit' => $netProfit,
            'inflow_by_category' => $inflowByCategory,
            'outflow_by_category' => $outflowByCategory,
            'recent_transactions' => Cashflow::with('order', 'creator')->latest()->take(10)->get(),
        ];
    }
}
