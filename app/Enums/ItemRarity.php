<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemRarity: string
{
    case Common = 'common';
    case Rare = 'rare';
    case Epic = 'epic';
    case Legendary = 'legendary';

    public function label(): string
    {
        return __('app.rarity_' . $this->value);
    }

    public function colorToken(): string
    {
        return match ($this) {
            self::Common => 'muted',
            self::Rare => 'blue',
            self::Epic => 'accent',
            self::Legendary => 'yellow',
        };
    }

    public function tier(): int
    {
        return match ($this) {
            self::Common => 1,
            self::Rare => 2,
            self::Epic => 3,
            self::Legendary => 4,
        };
    }
}
