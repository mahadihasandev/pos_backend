<x-layout title="Orders & Sales Dashboard">
    <!-- Top Metrics Overview (White Spectrum) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-card class="bg-white hover:shadow-md transition">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Today's Gross Sales</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1.5">৳{{ number_format($metrics['gross_revenue'] ?? 0, 2) }}</h3>
            <p class="text-[11px] text-emerald-600 font-medium mt-2 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span>Redis Cached • 5m TTL</span>
            </p>
        </x-card>

        <x-card class="bg-white hover:shadow-md transition">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Orders</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1.5">{{ number_format($metrics['total_orders'] ?? 0) }}</h3>
            <p class="text-[11px] text-slate-500 mt-2">Active branch throughput</p>
        </x-card>

        <x-card class="bg-white hover:shadow-md transition">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Average Basket Value</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1.5">৳{{ number_format($metrics['average_order_value'] ?? 0, 2) }}</h3>
            <p class="text-[11px] text-slate-500 mt-2">Per checkout transaction</p>
        </x-card>

        <x-card class="bg-white hover:shadow-md transition">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completed Orders</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1.5">{{ number_format($metrics['completed_orders'] ?? 0) }}</h3>
            <p class="text-[11px] text-slate-500 mt-2">{{ $metrics['pending_orders'] ?? 0 }} pending fulfillment</p>
        </x-card>
    </div>

    <!-- Orders Table Section -->
    <x-card title="Supermarket Orders" subtitle="Live feed from POS checkouts and API clients">
        <x-slot:actions>
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by Order #..." 
                    class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                />
                <button type="submit" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                    Filter
                </button>
            </form>
        </x-slot:actions>

        <x-table>
            <x-slot:head>
                <th class="px-6 py-3.5">Order Number</th>
                <th class="px-6 py-3.5">Date & Time</th>
                <th class="px-6 py-3.5">Status</th>
                <th class="px-6 py-3.5">Payment</th>
                <th class="px-6 py-3.5 text-right">Total Amount</th>
            </x-slot:head>

            @forelse($orders as $order)
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="px-6 py-4 font-mono font-semibold text-indigo-600">
                        {{ $order->order_number }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $order->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusVal = $order->status instanceof \App\Enums\OrderStatus ? $order->status->value : (string) $order->status;
                            $statusLabel = $order->status instanceof \App\Enums\OrderStatus ? $order->status->label() : ucfirst($statusVal);
                            $badgeVariant = match($statusVal) {
                                'completed' => 'success',
                                'pending' => 'warning',
                                'cancelled', 'refunded' => 'danger',
                                default => 'info',
                            };
                        @endphp
                        <x-badge :variant="$badgeVariant">
                            {{ $statusLabel }}
                        </x-badge>
                    </td>
                    <td class="px-6 py-4 uppercase text-xs font-medium tracking-wider text-slate-600">
                        {{ $order->payment_method }}
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-slate-900">
                        ৳{{ number_format((float) $order->total_amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                        No orders recorded yet. Initiate checkout via the POS Cashier Terminal.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </x-card>
</x-layout>
