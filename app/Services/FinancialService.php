<?php

namespace App\Services;

use App\Models\Cashflow;
use App\Models\Order;

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

        // Calculate total inflow from cashflow records
        $totalInflowCashflow = $cashflows->where('type', 'inflow')->sum('amount');

        // Also check if there are paid orders not linked to a cashflow record
        $linkedOrderIds = $cashflows->whereNotNull('order_id')->pluck('order_id')->toArray();
        $unlinkedPaidOrdersSum = Order::whereIn('payment_status', ['lunas', 'dp'])
            ->whereNotIn('id', $linkedOrderIds)
            ->sum('total_amount');

        $totalInflow = $totalInflowCashflow + $unlinkedPaidOrdersSum;
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
