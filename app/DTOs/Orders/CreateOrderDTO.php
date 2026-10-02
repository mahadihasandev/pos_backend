<?php

declare(strict_types=1);

namespace App\DTOs\Orders;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;

readonly class CreateOrderDTO
{
    /**
     * @param array<int, OrderItemDTO> $items
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public int $tenantId,
        public ?int $customerId,
        public string $orderNumber,
        public OrderStatus $status,
        public string $paymentMethod,
        public array $items,
        public float $discountAmount = 0.0,
        public float $taxRate = 0.05, // 5% default tax
        public ?array $metadata = null,
    ) {}

    public function calculateSubtotal(): float
    {
        return round(array_sum(array_map(fn (OrderItemDTO $item) => $item->totalPrice(), $this->items)), 4);
    }

    public function calculateTax(float $subtotalAfterDiscount): float
    {
        return round($subtotalAfterDiscount * $this->taxRate, 4);
    }

    public function calculateTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        $subtotalAfterDiscount = max(0.0, $subtotal - $this->discountAmount);
        $tax = $this->calculateTax($subtotalAfterDiscount);

        return round($subtotalAfterDiscount + $tax, 4);
    }

    /**
     * Factory from validated request data
     *
     * @param array<string, mixed> $data
     */
    public static function fromValidated(array $data, int $tenantId): self
    {
        $items = array_map(
            fn (array $itemData) => OrderItemDTO::fromArray($itemData),
            $data['items']
        );

        return new self(
            tenantId: $tenantId,
            customerId: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            orderNumber: (string) ($data['order_number'] ?? 'ORD-' . strtoupper(bin2hex(random_bytes(4)))),
            status: OrderStatus::from((string) ($data['status'] ?? OrderStatus::PENDING->value)),
            paymentMethod: (string) ($data['payment_method'] ?? 'card'),
            items: $items,
            discountAmount: (float) ($data['discount_amount'] ?? 0.0),
            taxRate: (float) ($data['tax_rate'] ?? 0.05),
            metadata: $data['metadata'] ?? null,
        );
    }
}
