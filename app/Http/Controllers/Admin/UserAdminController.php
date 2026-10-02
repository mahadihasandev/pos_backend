<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    public function __construct(
        private readonly CreateUserAction $createUserAction,
    ) {}

    /**
     * Display staff directory with role designation hierarchy.
     */
    public function index(Request $request): View
    {
        $tenantId = (int) (auth()->user()?->tenant_id ?? 1);

        $users = User::forTenant($tenantId)
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->query('role')))
            ->when($request->filled('search'), function ($q) use ($request): void {
                $search = (string) $request->query('search');
                $q->where(fn ($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Create a new staff account (Manager / Admin only).
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->isManager(), 403, 'Only Branch Managers or Admins can create staff accounts.');

        $tenantId = (int) (auth()->user()?->tenant_id ?? 1);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where('tenant_id', $tenantId),
            ],
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:cashier,floor_supervisor,branch_manager',
            'pin_code' => [
                'required',
                'string',
                'min:4',
                'max:8',
                Rule::unique('users')->where('tenant_id', $tenantId),
            ],
            'phone' => 'nullable|string|max:20',
        ]);

        $this->createUserAction->execute(array_merge($validated, ['tenant_id' => $tenantId]));

        return redirect()->route('admin.users.index')->with('success', 'Staff account created successfully.');
    }

    /**
     * Toggle active status of a staff member.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        abort_unless(auth()->user()?->isManager(), 403, 'Unauthorized action.');

        $user = User::where('tenant_id', auth()->user()?->tenant_id ?? 1)->findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', "Staff status updated to " . ($user->is_active ? 'Active' : 'Inactive'));
    }
}
