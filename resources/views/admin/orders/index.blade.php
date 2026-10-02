<x-layout title="Orders & Sales Dashboard">
    <!-- Top Metrics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-card class="bg-gradient-to-br from-slate-900 to-slate-900/50">
            <p class="text-xs font-medium text-slate-400">Today's Gross Sales</p>
            <h3 class="text-2xl font-bold text-white mt-1">৳{{ number_format($metrics['gross_revenue'] ?? 0, 2) }}</h3>
            <p class="text-[11px] text-emerald-400 mt-2 flex items-center gap-1">
                <span>↑ Redis Cached</span>
                <span class="text-slate-500">• 5m TTL</span>
            </p>
        </x-card>

        <x-card class="bg-gradient-to-br from-slate-900 to-slate-900/50">
            <p class="text-xs font-medium text-slate-400">Total Orders</p>
            <h3 class="text-2xl font-bold text-white mt-1">{{ number_format($metrics['total_orders'] ?? 0) }}</h3>
            <p class="text-[11px] text-slate-400 mt-2">Active branch throughput</p>
        </x-card>

        <x-card class="bg-gradient-to-br from-slate-900 to-slate-900/50">
            <p class="text-xs font-medium text-slate-400">Average Order Value</p>
            <h3 class="text-2xl font-bold text-white mt-1">৳{{ number_format($metrics['average_order_value'] ?? 0, 2) }}</h3>
            <p class="text-[11px] text-slate-400 mt-2">Per checkout basket</p>
        </x-card>

        <x-card class="bg-gradient-to-br from-slate-900 to-slate-900/50">
            <p class="text-xs font-medium text-slate-400">Completed Orders</p>
            <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ number_format($metrics['completed_orders'] ?? 0) }}</h3>
            <p class="text-[11px] text-slate-400 mt-2">{{ $metrics['pending_orders'] ?? 0 }} pending fulfillment</p>
        </x-card>
    </div>

    <!-- Orders Table Section -->
    <x-card title="Recent Orders" subtitle="Live feed from POS checkouts and API clients">
        <x-slot:actions>
            <form method="GET" action="/admin/orders" class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by Order #..." 
                    class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium rounded-lg transition">
                    Filter
                </button>
            </form>
        </x-slot:actions>

        <x-table>
            <x-slot:head>
                <th class="px-6 py-3">Order Number</th>
                <th class="px-6 py-3">Date</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Payment</th>
                <th class="px-6 py-3 text-right">Total Amount</th>
            </x-slot:head>

            @forelse($orders as $order)
                <tr class="hover:bg-slate-850/50 transition">
                    <td class="px-6 py-4 font-mono font-medium text-indigo-400">
                        {{ $order->order_number }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400">
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
                    <td class="px-6 py-4 uppercase text-xs tracking-wider text-slate-300">
                        {{ $order->payment_method }}
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-white">
                        ৳{{ number_format((float) $order->total_amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-sm">
                        No orders recorded yet. Initiate checkout via Next.js or API.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </x-card>
</x-layout>
