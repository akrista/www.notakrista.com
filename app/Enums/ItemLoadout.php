<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemLoadout: string
{
    case Ranked = 'ranked';
    case Casual = 'casual';

    public function label(): string
    {
        return __('app.loadout_' . $this->value);
    }

    public function suffix(): string
    {
        return match ($this) {
            self::Ranked => 'PVP',
            self::Casual => 'PVE',
        };
    }
}
