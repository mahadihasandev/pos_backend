<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Actions\Customers\CreateCustomerAction;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CreateCustomerAction $createCustomerAction,
    ) {}

    /**
     * Search and list active customers for the POS checkout terminal.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = (int) $request->header('X-Tenant-Id', 1);

        $customers = Customer::forTenant($tenantId)
            ->active()
            ->when($request->filled('search'), function ($q) use ($request): void {
                $search = (string) $request->query('search');
                $q->where(fn ($sub) => $sub->where('phone', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            })
            ->limit(20)
            ->get(['id', 'name', 'phone', 'email', 'loyalty_points', 'credit_balance', 'credit_limit']);

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    /**
     * Fast customer registration from POS counter.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $tenantId = (int) $request->header('X-Tenant-Id', 1);
        $customer = $this->createCustomerAction->execute(array_merge($validated, ['tenant_id' => $tenantId]));

        return response()->json([
            'success' => true,
            'message' => 'Customer registered successfully.',
            'data' => $customer,
        ]);
    }
}
