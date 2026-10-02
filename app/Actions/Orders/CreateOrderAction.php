<?php

declare(strict_types=1);

namespace App\Actions\Orders;

use App\DTOs\Orders\CreateOrderDTO;
use App\DTOs\Orders\OrderItemDTO;
use App\Exceptions\InsufficientStockException;
use App\Jobs\ProcessOrderFulfillmentJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Analytics\SalesMetricsCacheService;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function __construct(
        private readonly SalesMetricsCacheService $metricsCacheService,
    ) {}

    /**
     * Execute checkout transactionally with pessimistic locking (lockForUpdate)
     * preventing race conditions when multiple lanes sell the last stock unit simultaneously.
     *
     * @throws InsufficientStockException
     */
    public function execute(CreateOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto): Order {
            $subtotal = $dto->calculateSubtotal();
            $subtotalAfterDiscount = max(0.0, $subtotal - $dto->discountAmount);
            $taxAmount = $dto->calculateTax($subtotalAfterDiscount);
            $totalAmount = $dto->calculateTotal();

            // 1. Pessimistic Concurrency Lock & Real-time Stock Deduction
            $productCostMap = [];
            foreach ($dto->items as $itemDTO) {
                if ($itemDTO->productId > 0) {
                    /** @var Product|null $product */
                    $product = Product::forTenant($dto->tenantId)
                        ->where('id', $itemDTO->productId)
                        ->lockForUpdate() // SELECT ... FOR UPDATE (PostgreSQL row-level lock)
                        ->first();

                    if ($product) {
                        if (! $product->hasSufficientStock((float) $itemDTO->quantity)) {
                            throw new InsufficientStockException(
                                "Insufficient inventory for [{$product->name}]. Requested: {$itemDTO->quantity}, Available: {$product->stock_quantity}"
                            );
                        }

                        // Atomic stock decrement inside transactional lock
                        $product->decrement('stock_quantity', $itemDTO->quantity);
                        $productCostMap[$itemDTO->productId] = (float) $product->cost_price;
                    }
                }
            }

            // 2. Retail CRM & Khata Customer Ledger Synchronization
            $isCreditSale = ($dto->paymentMethod === 'credit');
            $dueAmount = $isCreditSale ? $totalAmount : 0.0;
            $paidAmount = $isCreditSale ? 0.0 : $totalAmount;
            $loyaltyEarned = $isCreditSale ? 0 : (int) floor($totalAmount / 100); // 1 point per ৳100 spent

            if ($dto->customerId) {
                /** @var \App\Models\Customer|null $customer */
                $customer = \App\Models\Customer::forTenant($dto->tenantId)->find($dto->customerId);
                if ($customer) {
                    $customer->increment('orders_count');
                    $customer->increment('total_spent', $totalAmount);

                    if ($isCreditSale) {
                        $customer->increment('credit_balance', $dueAmount);
                    } elseif ($loyaltyEarned > 0) {
                        $customer->increment('loyalty_points', $loyaltyEarned);
                    }
                }
            }

            // 3. Persist master order record
            $order = Order::create([
                'tenant_id' => $dto->tenantId,
                'customer_id' => $dto->customerId,
                'order_number' => $dto->orderNumber,
                'status' => $dto->status,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $dto->discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => 0.0,
                'loyalty_points_earned' => $loyaltyEarned,
                'is_credit_sale' => $isCreditSale,
                'due_amount' => $dueAmount,
                'payment_method' => $dto->paymentMethod,
                'metadata' => $dto->metadata,
            ]);

            // 4. Batch insert order items with accurate cost prices for COGS reporting
            $itemsData = array_map(function (OrderItemDTO $itemDTO) use ($order, $productCostMap): array {
                $costPrice = $productCostMap[$itemDTO->productId] ?? round($itemDTO->unitPrice * 0.70, 4);

                return [
                    'order_id' => $order->id,
                    'product_id' => $itemDTO->productId,
                    'sku' => $itemDTO->sku,
                    'product_name' => $itemDTO->productName,
                    'quantity' => $itemDTO->quantity,
                    'unit_price' => $itemDTO->unitPrice,
                    'cost_price' => $costPrice,
                    'discount_amount' => 0.0,
                    'total_price' => $itemDTO->totalPrice(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $dto->items);

            OrderItem::insert($itemsData);

            // 4. Invalidate Redis analytics cache
            $this->metricsCacheService->invalidateTenantMetrics($dto->tenantId);

            // 5. Dispatch async post-fulfillment background job
            ProcessOrderFulfillmentJob::dispatch($order->id);

            return $order->load('items');
        });
    }
}
