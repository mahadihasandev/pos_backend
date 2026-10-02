<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\v1;

use App\DTOs\Orders\CreateOrderDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Add multi-tenant authorization logic (e.g. check user has tenant access)
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer'],
            'order_number' => ['nullable', 'string', 'max:64'],
            'payment_method' => ['required', 'string', Rule::in(['cash', 'card', 'mobile_banking', 'credit'])],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'metadata' => ['nullable', 'array'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.sku' => ['required', 'string', 'max:64'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Transform request data directly into a verified DTO
     */
    public function toDTO(int $tenantId): CreateOrderDTO
    {
        return CreateOrderDTO::fromValidated($this->validated(), $tenantId);
    }
}
