<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateUserAction
{
    /**
     * Create a staff account with designation and access PIN.
     *
     * @param array{
     *     tenant_id: int,
     *     name: string,
     *     email: string,
     *     password: string,
     *     role: string,
     *     pin_code: string,
     *     phone?: string|null,
     * } $data
     * @return User
     * @throws ValidationException
     */
    public function execute(array $data): User
    {
        $tenantId = (int) $data['tenant_id'];

        // Enforce uniqueness within tenant
        if (User::forTenant($tenantId)->where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['A staff member with this email already exists in this branch.'],
            ]);
        }

        if (User::forTenant($tenantId)->where('pin_code', $data['pin_code'])->exists()) {
            throw ValidationException::withMessages([
                'pin_code' => ['This POS terminal PIN is already assigned to another staff member.'],
            ]);
        }

        return User::create([
            'tenant_id' => $tenantId,
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'pin_code' => trim($data['pin_code']),
            'phone' => isset($data['phone']) ? trim($data['phone']) : null,
            'is_active' => true,
            'can_sell' => $data['can_sell'] ?? true,
        ]);
    }
}
