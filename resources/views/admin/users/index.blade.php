<x-layout title="Staff & Multiuser Access Management">
    <div x-data="{ isCreateOpen: false }">
        <!-- Top Statistics & Quick Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Staff Hierarchy & Designations</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Branch Managers and Admins can create and control POS terminal cashier and supervisor accounts
                </p>
            </div>

            @if(auth()->user()?->isManager())
                <button 
                    type="button" 
                    @click="isCreateOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-500/20 transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Add Staff Member</span>
                </button>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Staff Table Card -->
        <x-card title="Branch Staff Directory" subtitle="Active POS cashiers, floor supervisors, and managers">
            <x-slot:actions>
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    <select 
                        name="role" 
                        onchange="this.form.submit()"
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                        <option value="">All Designations</option>
                        <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Cashiers</option>
                        <option value="floor_supervisor" {{ request('role') === 'floor_supervisor' ? 'selected' : '' }}>Floor Supervisors</option>
                        <option value="branch_manager" {{ request('role') === 'branch_manager' ? 'selected' : '' }}>Branch Managers</option>
                    </select>

                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search name or email..." 
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    />

                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition">
                        Filter
                    </button>
                </form>
            </x-slot:actions>

            <x-table>
                <x-slot:head>
                    <th class="px-6 py-3.5">Staff Member</th>
                    <th class="px-6 py-3.5">Designation</th>
                    <th class="px-6 py-3.5">POS Terminal PIN</th>
                    <th class="px-6 py-3.5">Phone</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </x-slot:head>

                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 leading-tight">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-500 font-mono">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            @php
                                $roleBadge = match($user->role) {
                                    'branch_manager', 'super_admin' => 'default',
                                    'floor_supervisor' => 'info',
                                    default => 'success',
                                };
                            @endphp
                            <x-badge :variant="$roleBadge">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </x-badge>
                        </td>

                        <td class="px-6 py-4 font-mono font-bold text-xs text-indigo-700">
                            {{ $user->pin_code ? str_pad($user->pin_code, 4, '•', STR_PAD_LEFT) : 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-xs text-slate-600 font-mono">
                            {{ $user->phone ?? '—' }}
                        </td>

                        <td class="px-6 py-4">
                            <x-badge :variant="$user->is_active ? 'success' : 'danger'">
                                {{ $user->is_active ? 'Active' : 'Disabled' }}
                            </x-badge>
                        </td>

                        <td class="px-6 py-4 text-right">
                            @if(auth()->user()?->isManager() && $user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="text-xs font-semibold {{ $user->is_active ? 'text-rose-600 hover:text-rose-700' : 'text-emerald-600 hover:text-emerald-700' }} transition"
                                    >
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">Current User</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                            No staff accounts found matching filter.
                        </td>
                    </tr>
                @endforelse
            </x-table>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </x-card>

        <!-- Create Staff Modal (Alpine.js) -->
        <div 
            x-show="isCreateOpen" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs animate-in fade-in"
        >
            <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden" @click.away="isCreateOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Create Staff Account</h3>
                        <p class="text-[11px] text-slate-500">Configure role designation and terminal PIN</p>
                    </div>
                    <button @click="isCreateOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
                        ✕
                    </button>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            required 
                            placeholder="e.g. Tanvir Ahmed" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Designation / Role</label>
                            <select 
                                name="role" 
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >
                                <option value="cashier">Terminal Cashier</option>
                                <option value="floor_supervisor">Floor Supervisor</option>
                                <option value="branch_manager">Branch Manager</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Terminal PIN (4-8 digits)</label>
                            <input 
                                type="text" 
                                name="pin_code" 
                                required 
                                maxlength="8"
                                placeholder="e.g. 5566" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            placeholder="tanvir@supershop.com" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                            <input 
                                type="password" 
                                name="password" 
                                required 
                                minlength="6"
                                placeholder="••••••••" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Contact Phone</label>
                            <input 
                                type="text" 
                                name="phone" 
                                placeholder="017XXXXXXXX" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            />
                        </div>
                    </div>

                    <div class="pt-3 flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="isCreateOpen = false" 
                            class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="flex-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-500/20 transition"
                        >
                            Save Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
