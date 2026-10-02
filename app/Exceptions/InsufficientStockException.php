<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(
        string $message = 'One or more items in your cart exceed available supermarket inventory.',
        public readonly int $statusCode = 422
    ) {
        parent::__construct($message, $statusCode);
    }
}
