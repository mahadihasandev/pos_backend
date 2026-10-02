<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Analytics\SalesMetricsCacheService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function __construct(
        private readonly SalesMetricsCacheService $metricsCacheService,
    ) {}

    /**
     * Display the Admin Orders Management Dashboard.
     */
    public function index(Request $request): View
    {
        $tenantId = (int) (auth()->user()?->tenant_id ?? 1);

        $metrics = $this->metricsCacheService->getTenantDailyMetrics($tenantId);

        $orders = Order::forTenant($tenantId)
            ->with('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->query('search') . '%');
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', [
            'metrics' => $metrics,
            'orders' => $orders,
        ]);
    }
}
