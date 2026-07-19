<?php

declare(strict_types=1);

namespace App\Enums;

enum EquipmentSlot: string
{
    case Head = 'head';
    case Chest = 'chest';
    case MainHand = 'main_hand';
    case OffHand = 'off_hand';
    case Accessory1 = 'acc_1';
    case Accessory2 = 'acc_2';

    public function label(): string
    {
        return __('app.slot_' . $this->value);
    }

    public function position(): int
    {
        return match ($this) {
            self::Head => 1,
            self::Chest => 2,
            self::MainHand => 3,
            self::OffHand => 4,
            self::Accessory1 => 5,
            self::Accessory2 => 6,
        };
    }
}
