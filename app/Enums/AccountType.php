<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountType: string
{
    case Bank = 'bank';
    case Exchange = 'exchange';
    case Wallet = 'wallet';
    case Cash = 'cash';
    case Other = 'other';

    public function label(): string
    {
        return __('app.account_type_' . $this->value);
    }

    public function colorToken(): string
    {
        return match ($this) {
            self::Bank => 'blue',
            self::Exchange => 'yellow',
            self::Wallet => 'primary',
            self::Cash => 'accent',
            self::Other => 'muted',
        };
    }
}
