<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionDirection: string
{
    case Inflow = 'inflow';
    case Outflow = 'outflow';

    public function label(): string
    {
        return __('app.transaction_direction_' . $this->value);
    }

    public function colorToken(): string
    {
        return match ($this) {
            self::Inflow => 'accent',
            self::Outflow => 'red',
        };
    }
}
