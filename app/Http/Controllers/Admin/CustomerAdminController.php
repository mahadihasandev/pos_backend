<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Customers\CreateCustomerAction;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerAdminController extends Controller
{
    public function __construct(
        private readonly CreateCustomerAction $createCustomerAction,
    ) {}

    /**
     * Display CRM customers ledger and loyalty balances.
     */
    public function index(Request $request): View
    {
        $tenantId = (int) (auth()->user()?->tenant_id ?? 1);

        $customers = Customer::forTenant($tenantId)
            ->when($request->filled('search'), function ($q) use ($request): void {
                $search = (string) $request->query('search');
                $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
            })
            ->when($request->query('filter') === 'with_dues', fn ($q) => $q->where('credit_balance', '>', 0))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $totalKhataDue = (float) Customer::forTenant($tenantId)->sum('credit_balance');
        $totalCustomers = Customer::forTenant($tenantId)->count();

        return view('admin.customers.index', [
            'customers' => $customers,
            'totalKhataDue' => $totalKhataDue,
            'totalCustomers' => $totalCustomers,
        ]);
    }

    /**
     * Enroll a new customer from the admin dashboard.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenantId = (int) (auth()->user()?->tenant_id ?? 1);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers')->where('tenant_id', $tenantId),
            ],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->createCustomerAction->execute(array_merge($validated, ['tenant_id' => $tenantId]));

        return redirect()->route('admin.customers.index')->with('success', 'Customer enrolled into CRM successfully.');
    }
}
