<x-layout title="Customer Relationship Management (CRM)">
    <div x-data="{ isCreateOpen: false }">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            <x-card class="bg-white">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total CRM Customers</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1.5">{{ number_format($totalCustomers) }}</h3>
                <p class="text-[11px] text-slate-400 mt-2">Active loyalty profiles</p>
            </x-card>

            <x-card class="bg-white">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding Khata / Due Balance</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-1.5">৳{{ number_format($totalKhataDue, 2) }}</h3>
                <p class="text-[11px] text-slate-400 mt-2">Receivable from VIP & credit customers</p>
            </x-card>

            <div class="flex items-center justify-end">
                <button 
                    type="button" 
                    @click="isCreateOpen = true"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-500/20 transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>+ Enroll New Customer</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Customers Directory Table -->
        <x-card title="Customer Directory & Khata Ledger" subtitle="Manage retail loyalty and customer dues">
            <x-slot:actions>
                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-2">
                    <a 
                        href="{{ route('admin.customers.index', ['filter' => request('filter') === 'with_dues' ? null : 'with_dues']) }}" 
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold border transition {{ request('filter') === 'with_dues' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}"
                    >
                        {{ request('filter') === 'with_dues' ? '✓ Showing Dues Only' : 'Filter Dues Only' }}
                    </a>

                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search name or phone..." 
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    />

                    <button type="submit" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition">
                        Search
                    </button>
                </form>
            </x-slot:actions>

            <x-table>
                <x-slot:head>
                    <th class="px-6 py-3.5">Customer</th>
                    <th class="px-6 py-3.5">Phone Number</th>
                    <th class="px-6 py-3.5">Loyalty Points</th>
                    <th class="px-6 py-3.5">Credit Limit</th>
                    <th class="px-6 py-3.5 text-right">Khata Due Balance</th>
                </x-slot:head>

                @forelse($customers as $customer)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center border border-indigo-100">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $customer->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $customer->email ?? 'No email recorded' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 font-mono text-xs font-bold text-slate-700">
                            {{ $customer->phone }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                ★ {{ number_format($customer->loyalty_points) }} pts
                            </span>
                        </td>

                        <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                            ৳{{ number_format((float) $customer->credit_limit, 2) }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            @if((float) $customer->credit_balance > 0)
                                <span class="font-bold text-rose-600">
                                    ৳{{ number_format((float) $customer->credit_balance, 2) }}
                                </span>
                            @else
                                <span class="text-xs font-semibold text-emerald-600">
                                    Clear (৳0.00)
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                            No customers found. Enroll your first customer using the button above.
                        </td>
                    </tr>
                @endforelse
            </x-table>

            <div class="mt-4">
                {{ $customers->links() }}
            </div>
        </x-card>

        <!-- Enroll Customer Modal -->
        <div 
            x-show="isCreateOpen" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs animate-in fade-in"
        >
            <div class="bg-white border border-slate-200 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden" @click.away="isCreateOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Enroll New Customer</h3>
                        <p class="text-[11px] text-slate-500">Add to loyalty rewards and credit ledger</p>
                    </div>
                    <button @click="isCreateOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">
                        ✕
                    </button>
                </div>

                <form action="{{ route('admin.customers.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Customer Full Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            required 
                            placeholder="e.g. Farhana Yasmin" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number</label>
                            <input 
                                type="text" 
                                name="phone" 
                                required 
                                placeholder="01XXXXXXXXX" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Khata Credit Limit (৳)</label>
                            <input 
                                type="number" 
                                name="credit_limit" 
                                step="100" 
                                value="5000" 
                                placeholder="5000" 
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email (Optional)</label>
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="customer@example.com" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Delivery / Residence Address</label>
                        <input 
                            type="text" 
                            name="address" 
                            placeholder="Flat #4B, Road #7, Dhanmondi" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        />
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
                            Enroll Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
