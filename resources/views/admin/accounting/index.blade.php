<x-layout title="Retail Accounting & Financial Reports">
    <!-- Top Financial Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <x-card class="bg-white">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Net Realized Revenue</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-1.5">৳{{ number_format($netRevenue, 2) }}</h3>
            <p class="text-[11px] text-emerald-600 font-medium mt-2">After discounts • POS terminals</p>
        </x-card>

        <x-card class="bg-white">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estimated Gross Profit</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1.5">৳{{ number_format($grossProfit, 2) }}</h3>
            <p class="text-[11px] text-slate-400 mt-2">Margin: {{ number_format($profitMargin, 1) }}%</p>
        </x-card>

        <x-card class="bg-white">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">VAT / Mushak-6.3 Collected</p>
            <h3 class="text-2xl font-bold text-indigo-600 mt-1.5">৳{{ number_format($totalTax, 2) }}</h3>
            <p class="text-[11px] text-slate-400 mt-2">Government tax liability</p>
        </x-card>

        <x-card class="bg-white">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Operating Expenses</p>
            <h3 class="text-2xl font-bold text-rose-600 mt-1.5">৳{{ number_format($totalExpenses, 2) }}</h3>
            <p class="text-[11px] text-slate-400 mt-2">Utilities, rent, supplies</p>
        </x-card>
    </div>

    <!-- Payment Reconciliation Matrix -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <x-card class="bg-white" title="Cash Drawer Settlement">
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-xl font-bold text-slate-900">৳{{ number_format($cashSales, 2) }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Cash In Hand</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Physical currency collected at cashier terminals</p>
        </x-card>

        <x-card class="bg-white" title="Card POS Terminals">
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-xl font-bold text-slate-900">৳{{ number_format($cardSales, 2) }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">Bank Batch</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Visa, Mastercard, & NexusPay settlements</p>
        </x-card>

        <x-card class="bg-white" title="bKash / Mobile Banking">
            <div class="flex items-baseline justify-between mt-1">
                <span class="text-xl font-bold text-slate-900">৳{{ number_format($mfsSales, 2) }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">MFS Merchant</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Digital wallet QR code & counter transfers</p>
        </x-card>
    </div>

    <!-- Cash Movement & Shift Audit Trail -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Safe Drops & Cash Movements -->
        <x-card title="Cash Movement Audit" subtitle="Drops to safe, drawer float, and petty payouts">
            <x-table>
                <x-slot:head>
                    <th class="px-5 py-3">Time</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Cashier</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                </x-slot:head>

                @forelse($cashMovements as $move)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-xs text-slate-500">
                            {{ $move->created_at->format('M d, H:i') }}
                        </td>
                        <td class="px-5 py-3">
                            <x-badge :variant="$move->type === 'cash_drop' ? 'success' : 'warning'">
                                {{ ucwords(str_replace('_', ' ', $move->type)) }}
                            </x-badge>
                        </td>
                        <td class="px-5 py-3 text-xs font-semibold text-slate-700">
                            {{ $move->user->name ?? 'Cashier' }}
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-slate-900">
                            ৳{{ number_format((float) $move->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-xs">
                            No cash drawer movements recorded today.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>

        <!-- Operational Expenses -->
        <x-card title="Operating Expenses" subtitle="Branch expenditures and store maintenance">
            <x-table>
                <x-slot:head>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3">Title</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                </x-slot:head>

                @forelse($expenses as $exp)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-xs text-slate-500">
                            {{ $exp->incurred_at ? $exp->incurred_at->format('M d') : 'Today' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs uppercase font-semibold text-slate-600">
                                {{ $exp->category }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-slate-800 font-medium">
                            {{ $exp->title }}
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-rose-600">
                            ৳{{ number_format((float) $exp->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-xs">
                            No operating expenses logged this month.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>
    </div>
</x-layout>
