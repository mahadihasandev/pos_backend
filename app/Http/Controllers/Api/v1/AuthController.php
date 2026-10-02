<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginAction $loginAction,
    ) {}

    /**
     * Authenticate POS Cashier or Supervisor.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'nullable|email',
            'password' => 'nullable|string',
            'pin_code' => 'nullable|string|min:4|max:10',
            'tenant_id' => 'nullable|integer',
        ]);

        $tenantId = (int) $request->header('X-Tenant-Id', $validated['tenant_id'] ?? 1);
        $result = $this->loginAction->execute(array_merge($validated, ['tenant_id' => $tenantId]));

        return response()->json([
            'success' => true,
            'message' => 'Cashier authenticated successfully.',
            'data' => [
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'role' => $result['user']->role,
                    'pin_code' => $result['user']->pin_code,
                    'tenant_id' => $result['user']->tenant_id,
                ],
                'token' => $result['token'],
            ],
        ]);
    }

    /**
     * Retrieve the current authenticated cashier profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'pin_code' => $user->pin_code,
                'tenant_id' => $user->tenant_id,
            ],
        ]);
    }

    /**
     * Logout and revoke cashier token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
