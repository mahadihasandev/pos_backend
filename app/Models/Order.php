<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $tenant_id
 * @property int|null $customer_id
 * @property string $order_number
 * @property OrderStatus $status
 * @property float $subtotal
 * @property float $tax_amount
 * @property float $discount_amount
 * @property float $total_amount
 * @property string $payment_method
 * @property array<string, mixed>|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, OrderItem> $items
 */
class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'customer_id',
        'order_number',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tenant_id' => 'integer',
        'customer_id' => 'integer',
        'status' => OrderStatus::class,
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Scope: Filter by Tenant
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope: Filter by Status
     */
    public function scopeWithStatus(Builder $query, OrderStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }

    /**
     * Scope: Recent Orders
     */
    public function scopeRecent(Builder $query, int $limit = 20): Builder
    {
        return $query->latest('created_at')->limit($limit);
    }
}
