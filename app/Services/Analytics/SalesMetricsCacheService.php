<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SalesMetricsCacheService
{
    private const CACHE_TTL_SECONDS = 300; // 5 minutes cache
    private const CACHE_PREFIX = 'pos_metrics:tenant:';

    /**
     * Retrieve aggregated metrics with Redis caching and lock-based stampede protection.
     *
     * @return array<string, mixed>
     */
    public function getTenantDailyMetrics(int $tenantId): array
    {
        $cacheKey = self::CACHE_PREFIX . "{$tenantId}:daily";

        $store = Cache::store();

        // Fast path: cached read
        $cached = $store->get($cacheKey);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        // Slow path: acquire atomic lock to avoid 100+ concurrent requests hitting the DB simultaneously
        $lockKey = "lock:{$cacheKey}";

        try {
            $lock = $store->lock($lockKey, 10);

            // Wait up to 5 seconds to acquire lock
            return $lock->block(5, function () use ($store, $cacheKey, $tenantId): array {
                // Double check if another worker populated the cache while waiting
                $cached = $store->get($cacheKey);
                if ($cached !== null && is_array($cached)) {
                    return $cached;
                }

                // Execute heavy aggregated PostgreSQL query
                $today = now()->startOfDay();

                $metrics = Order::forTenant($tenantId)
                    ->where('created_at', '>=', $today)
                    ->selectRaw('
                        COUNT(id) as total_orders,
                        COALESCE(SUM(total_amount), 0) as gross_revenue,
                        COALESCE(AVG(total_amount), 0) as average_order_value,
                        COUNT(CASE WHEN status = \'completed\' THEN 1 END) as completed_orders,
                        COUNT(CASE WHEN status = \'pending\' THEN 1 END) as pending_orders
                    ')
                    ->first();

                $result = [
                    'tenant_id' => $tenantId,
                    'date' => $today->toDateString(),
                    'total_orders' => (int) ($metrics->total_orders ?? 0),
                    'gross_revenue' => (float) ($metrics->gross_revenue ?? 0.0),
                    'average_order_value' => round((float) ($metrics->average_order_value ?? 0.0), 2),
                    'completed_orders' => (int) ($metrics->completed_orders ?? 0),
                    'pending_orders' => (int) ($metrics->pending_orders ?? 0),
                    'generated_at' => now()->toIso8601String(),
                ];

                $store->put($cacheKey, $result, self::CACHE_TTL_SECONDS);

                return $result;
            });
        } catch (\Throwable $e) {
            // Fallback gracefully without lock if driver does not support atomic locks
            return $this->computeMetricsFallback($tenantId);
        }
    }

    /**
     * Fallback computation for non-locking cache stores
     *
     * @return array<string, mixed>
     */
    private function computeMetricsFallback(int $tenantId): array
    {
        $today = now()->startOfDay();

        $metrics = Order::forTenant($tenantId)
            ->where('created_at', '>=', $today)
            ->selectRaw('
                COUNT(id) as total_orders,
                COALESCE(SUM(total_amount), 0) as gross_revenue,
                COALESCE(AVG(total_amount), 0) as average_order_value,
                COUNT(CASE WHEN status = \'completed\' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN status = \'pending\' THEN 1 END) as pending_orders
            ')
            ->first();

        return [
            'tenant_id' => $tenantId,
            'date' => $today->toDateString(),
            'total_orders' => (int) ($metrics->total_orders ?? 0),
            'gross_revenue' => (float) ($metrics->gross_revenue ?? 0.0),
            'average_order_value' => round((float) ($metrics->average_order_value ?? 0.0), 2),
            'completed_orders' => (int) ($metrics->completed_orders ?? 0),
            'pending_orders' => (int) ($metrics->pending_orders ?? 0),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Invalidate tenant cache upon mutation
     */
    public function invalidateTenantMetrics(int $tenantId): void
    {
        $cacheKey = self::CACHE_PREFIX . "{$tenantId}:daily";
        Cache::store()->forget($cacheKey);
    }
}
