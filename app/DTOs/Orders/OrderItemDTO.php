<?php

declare(strict_types=1);

namespace App\DTOs\Orders;

readonly class OrderItemDTO
{
    public function __construct(
        public int $productId,
        public string $sku,
        public string $productName,
        public float $quantity,
        public float $unitPrice,
    ) {}

    public function totalPrice(): float
    {
        return round($this->quantity * $this->unitPrice, 4);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            sku: (string) $data['sku'],
            productName: (string) $data['product_name'],
            quantity: (float) $data['quantity'],
            unitPrice: (float) $data['unit_price'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'sku' => $this->sku,
            'product_name' => $this->productName,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'total_price' => $this->totalPrice(),
        ];
    }
}
