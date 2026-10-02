<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingAdminController extends Controller
{
    /**
     * Display comprehensive retail financial accounting and ledger overview.
     */
    public function index(Request $request): View
    {
        $tenantId = (int) (auth()->user()?->tenant_id ?? 1);

        // Sales Aggregations
        $grossRevenue = (float) Order::forTenant($tenantId)->where('status', 'completed')->sum('subtotal');
        $totalDiscount = (float) Order::forTenant($tenantId)->where('status', 'completed')->sum('discount_amount');
        $totalTax = (float) Order::forTenant($tenantId)->where('status', 'completed')->sum('tax_amount');
        $netRevenue = (float) Order::forTenant($tenantId)->where('status', 'completed')->sum('total_amount');

        // Payment Breakdown
        $cashSales = (float) Order::forTenant($tenantId)->where('status', 'completed')->where('payment_method', 'cash')->sum('total_amount');
        $cardSales = (float) Order::forTenant($tenantId)->where('status', 'completed')->where('payment_method', 'card')->sum('total_amount');
        $mfsSales = (float) Order::forTenant($tenantId)->where('status', 'completed')->where('payment_method', 'mobile_banking')->sum('total_amount');

        // Estimated COGS & Gross Profit (approx 70% cost ratio typical of supermarket groceries)
        $estimatedCogs = $grossRevenue * 0.72;
        $grossProfit = max(0.0, $netRevenue - $estimatedCogs);
        $profitMargin = $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0.0;

        // Cash Movement Ledgers
        $cashMovements = CashMovement::forTenant($tenantId)
            ->with(['user', 'authorizer'])
            ->latest('id')
            ->limit(10)
            ->get();

        // Expenses
        $expenses = Expense::forTenant($tenantId)
            ->with('user')
            ->latest('incurred_at')
            ->limit(10)
            ->get();
        $totalExpenses = (float) Expense::forTenant($tenantId)->sum('amount');

        return view('admin.accounting.index', [
            'grossRevenue' => $grossRevenue,
            'totalDiscount' => $totalDiscount,
            'totalTax' => $totalTax,
            'netRevenue' => $netRevenue,
            'cashSales' => $cashSales,
            'cardSales' => $cardSales,
            'mfsSales' => $mfsSales,
            'estimatedCogs' => $estimatedCogs,
            'grossProfit' => $grossProfit,
            'profitMargin' => $profitMargin,
            'cashMovements' => $cashMovements,
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
        ]);
    }
}
