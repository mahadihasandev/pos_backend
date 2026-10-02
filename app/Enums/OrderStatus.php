<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Payment',
            self::PAID => 'Paid',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            self::PAID => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            self::PROCESSING => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
            self::COMPLETED => 'bg-teal-500/10 text-teal-500 border-teal-500/20',
            self::CANCELLED => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
            self::REFUNDED => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
        };
    }
}
