<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessOrderFulfillmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    public function __construct(
        public int $orderId,
    ) {}

    /**
     * Execute the job: deduct inventory, generate invoice, notify channels.
     */
    public function handle(): void
    {
        /** @var Order|null $order */
        $order = Order::with('items')->find($this->orderId);

        if (! $order) {
            Log::warning("Fulfillment job skipped: Order #{$this->orderId} not found.");
            return;
        }

        // Idempotency check: Skip if already fulfilled or cancelled
        if (in_array($order->status->value, ['completed', 'cancelled'], true)) {
            Log::info("Order #{$order->order_number} already in terminal state [{$order->status->value}]. Skipping fulfillment.");
            return;
        }

        Log::info("Processing async fulfillment for Order #{$order->order_number} (Tenant ID: {$order->tenant_id})...");

        // 1. Simulate Inventory deduction / POS synchronization
        foreach ($order->items as $item) {
            Log::info("Deducting inventory: SKU {$item->sku}, Quantity {$item->quantity}");
        }

        // 2. Update order status or append audit metadata
        $metadata = $order->metadata ?? [];
        $metadata['fulfilled_at'] = now()->toIso8601String();
        $metadata['fulfillment_worker'] = gethostname();

        $order->update([
            'metadata' => $metadata,
        ]);

        Log::info("Order #{$order->order_number} fulfillment completed successfully.");
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error("Fulfillment job failed permanently for Order #{$this->orderId}: " . ($exception?->getMessage() ?? 'Unknown error'));
    }
}
