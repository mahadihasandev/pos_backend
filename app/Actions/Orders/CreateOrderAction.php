<?php

declare(strict_types=1);

namespace App\Actions\Orders;

use App\DTOs\Orders\CreateOrderDTO;
use App\DTOs\Orders\OrderItemDTO;
use App\Jobs\ProcessOrderFulfillmentJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Analytics\SalesMetricsCacheService;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function __construct(
        private readonly SalesMetricsCacheService $metricsCacheService,
    ) {}

    /**
     * Execute order creation transactionally.
     */
    public function execute(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto): Order {
            $subtotal = $dto->calculateSubtotal();
            $subtotalAfterDiscount = max(0.0, $subtotal - $dto->discountAmount);
            $taxAmount = $dto->calculateTax($subtotalAfterDiscount);
            $totalAmount = $dto->calculateTotal();

            // 1. Persist master order
            $order = Order::create([
                'tenant_id' => $dto->tenantId,
                'customer_id' => $dto->customerId,
                'order_number' => $dto->orderNumber,
                'status' => $dto->status,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $dto->discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $dto->paymentMethod,
                'metadata' => $dto->metadata,
            ]);

            // 2. Prepare items for batch insertion
            $itemsData = array_map(function (OrderItemDTO $itemDTO) use ($order): array {
                return [
                    'order_id' => $order->id,
                    'product_id' => $itemDTO->productId,
                    'sku' => $itemDTO->sku,
                    'product_name' => $itemDTO->productName,
                    'quantity' => $itemDTO->quantity,
                    'unit_price' => $itemDTO->unitPrice,
                    'total_price' => $itemDTO->totalPrice(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $dto->items);

            OrderItem::insert($itemsData);

            // 3. Invalidate cached sales metrics in Redis
            $this->metricsCacheService->invalidateTenantMetrics($dto->tenantId);

            // 4. Dispatch asynchronous background fulfillment job
            ProcessOrderFulfillmentJob::dispatch($order->id);

            return $order->load('items');
        });
    }
}
