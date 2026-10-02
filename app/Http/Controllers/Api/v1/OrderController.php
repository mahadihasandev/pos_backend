<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Actions\Orders\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreOrderRequest;
use App\Http\Resources\Api\v1\OrderResource;
use App\Models\Order;
use App\Services\Analytics\SalesMetricsCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private readonly CreateOrderAction $createOrderAction,
        private readonly SalesMetricsCacheService $metricsCacheService,
    ) {}

    /**
     * List tenant orders with pagination and filtering.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $tenantId = $this->resolveTenantId($request);

        $orders = Order::forTenant($tenantId)
            ->with('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->latest('created_at')
            ->paginate((int) $request->query('per_page', 15));

        return OrderResource::collection($orders);
    }

    /**
     * Store a new order via CreateOrderAction.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $tenantId = $this->resolveTenantId($request);
        $dto = $request->toDTO($tenantId);

        $order = $this->createOrderAction->execute($dto);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a single order by UUID.
     */
    public function show(string $uuid, Request $request): OrderResource
    {
        $tenantId = $this->resolveTenantId($request);

        $order = Order::forTenant($tenantId)
            ->with('items')
            ->where('uuid', $uuid)
            ->firstOrFail();

        return new OrderResource($order);
    }

    /**
     * High-speed cached daily sales & POS metrics.
     */
    public function metrics(Request $request): JsonResponse
    {
        $tenantId = $this->resolveTenantId($request);
        $data = $this->metricsCacheService->getTenantDailyMetrics($tenantId);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function resolveTenantId(Request $request): int
    {
        $header = $request->header('X-Tenant-Id');

        return ($header && is_numeric($header)) ? (int) $header : 1;
    }
}
