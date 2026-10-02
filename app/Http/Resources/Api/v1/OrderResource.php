<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\v1;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'tenant_id' => $this->tenant_id,
            'order_number' => $this->order_number,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'financials' => [
                'subtotal' => (float) $this->subtotal,
                'tax_amount' => (float) $this->tax_amount,
                'discount_amount' => (float) $this->discount_amount,
                'total_amount' => (float) $this->total_amount,
            ],
            'payment_method' => $this->payment_method,
            'metadata' => $this->metadata,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
