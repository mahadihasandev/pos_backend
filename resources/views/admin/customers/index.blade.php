<x-layout title="Customer Relationship Management (CRM)">
    <div x-data="{ isCreateOpen: false }">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
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

            <a 
                href="https://wa.me/8801735696417" 
                target="_blank" 
                class="flex flex-col justify-between p-5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-md shadow-emerald-500/20 transition group"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">WhatsApp CRM Desk</span>
                    <svg class="w-5 h-5 fill-current text-white" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                </div>
                <div class="mt-2">
                    <p class="text-lg font-black tracking-tight font-mono">+8801735696417</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-100 group-hover:underline mt-1">
                        <span>Open WhatsApp Support</span>
                        <span>&rarr;</span>
                    </span>
                </div>
            </a>

            <div class="flex items-center justify-end">
                <button 
                    type="button" 
                    @click="isCreateOpen = true"
                    class="w-full h-full inline-flex items-center justify-center gap-2 p-5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-500/20 transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <th class="px-6 py-3.5">Khata Due Balance</th>
                    <th class="px-6 py-3.5 text-right">CRM Actions</th>
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

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-700">{{ $customer->phone }}</span>
                                <a 
                                    href="https://wa.me/{{ $customer->getFormattedWhatsappPhone() }}" 
                                    target="_blank" 
                                    title="Open WhatsApp chat with {{ $customer->name }}"
                                    class="p-1 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 transition"
                                >
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                ★ {{ number_format($customer->loyalty_points) }} pts
                            </span>
                        </td>

                        <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                            ৳{{ number_format((float) $customer->credit_limit, 2) }}
                        </td>

                        <td class="px-6 py-4">
                            @if((float) $customer->credit_balance > 0)
                                <span class="font-bold text-rose-600 text-xs">
                                    ৳{{ number_format((float) $customer->credit_balance, 2) }}
                                </span>
                            @else
                                <span class="text-xs font-semibold text-emerald-600">
                                    Clear (৳0.00)
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if((float) $customer->credit_balance > 0)
                                    <a 
                                        href="{{ $customer->getWhatsappDueNoticeUrl('+8801735696417') }}" 
                                        target="_blank" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 text-[11px] font-bold transition shadow-2xs"
                                        title="Send polite due payment notice via WhatsApp"
                                    >
                                        <svg class="w-3 h-3 fill-current text-emerald-600" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                        <span>Due Notice</span>
                                    </a>
                                @endif
                                <a 
                                    href="https://wa.me/{{ $customer->getFormattedWhatsappPhone() }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 text-[11px] font-bold transition"
                                    title="Chat directly on WhatsApp"
                                >
                                    <svg class="w-3 h-3 fill-current text-emerald-600" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
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
