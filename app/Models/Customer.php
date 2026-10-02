<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'address',
        'loyalty_points',
        'credit_balance',
        'credit_limit',
        'total_spent',
        'orders_count',
        'notes',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tenant_id' => 'integer',
            'loyalty_points' => 'integer',
            'credit_balance' => 'decimal:4',
            'credit_limit' => 'decimal:4',
            'total_spent' => 'decimal:4',
            'orders_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeWithDues(Builder $query): Builder
    {
        return $query->where('credit_balance', '>', 0);
    }

    public function scopeByPhone(Builder $query, string $phone): Builder
    {
        return $query->where('phone', $phone);
    }

    public function canAcceptCredit(float $additionalDue): bool
    {
        $limit = (float) $this->credit_limit;
        if ($limit <= 0) {
            return false;
        }

        return ((float) $this->credit_balance + $additionalDue) <= $limit;
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
