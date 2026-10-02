<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * Authenticate a cashier or staff member via email/password or PIN code.
     *
     * @param array{
     *     tenant_id?: int,
     *     email?: string|null,
     *     password?: string|null,
     *     pin_code?: string|null
     * } $credentials
     * @return array{user: User, token: string}
     * @throws ValidationException
     */
    public function execute(array $credentials): array
    {
        $tenantId = (int) ($credentials['tenant_id'] ?? 1);

        /** @var User|null $user */
        $user = null;

        // 1. PIN code authentication (Supermarket POS terminal fast checkout mode)
        if (! empty($credentials['pin_code'])) {
            $user = User::forTenant($tenantId)
                ->where('pin_code', (string) $credentials['pin_code'])
                ->where('is_active', true)
                ->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'pin_code' => ['Invalid cashier PIN code or inactive account.'],
                ]);
            }
        }
        // 2. Email & Password authentication
        elseif (! empty($credentials['email']) && ! empty($credentials['password'])) {
            $user = User::forTenant($tenantId)
                ->where('email', (string) $credentials['email'])
                ->where('is_active', true)
                ->first();

            if (! $user || ! Hash::check((string) $credentials['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials do not match our records.'],
                ]);
            }
        } else {
            throw ValidationException::withMessages([
                'credentials' => ['Please provide either cashier PIN code or email and password.'],
            ]);
        }

        // 3. Issue a personal access token for POS terminal sessions
        $token = $user->createToken('pos-terminal-session', [$user->role])->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
