<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class CreateCustomerAction
{
    /**
     * Create or enroll a customer into the retail CRM.
     *
     * @param array{
     *     tenant_id: int,
     *     name: string,
     *     phone: string,
     *     email?: string|null,
     *     address?: string|null,
     *     credit_limit?: float|null,
     *     notes?: string|null,
     * } $data
     * @return Customer
     * @throws ValidationException
     */
    public function execute(array $data): Customer
    {
        $tenantId = (int) $data['tenant_id'];
        $phone = trim($data['phone']);

        if (Customer::forTenant($tenantId)->where('phone', $phone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => ['A customer with this phone number is already registered.'],
            ]);
        }

        return Customer::create([
            'tenant_id' => $tenantId,
            'name' => trim($data['name']),
            'phone' => $phone,
            'email' => isset($data['email']) ? strtolower(trim($data['email'])) : null,
            'address' => isset($data['address']) ? trim($data['address']) : null,
            'credit_limit' => (float) ($data['credit_limit'] ?? 0.0),
            'credit_balance' => 0.0,
            'loyalty_points' => 0,
            'notes' => isset($data['notes']) ? trim($data['notes']) : null,
            'is_active' => true,
        ]);
    }
}
