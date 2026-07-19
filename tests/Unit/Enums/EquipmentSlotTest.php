<?php

declare(strict_types=1);

use App\Enums\EquipmentSlot;

test('has six slots with stable position ordering', function (): void {
    $positions = collect(EquipmentSlot::cases())->map(fn (EquipmentSlot $slot): int => $slot->position())->all();

    expect($positions)->toBe([1, 2, 3, 4, 5, 6])
        ->and(EquipmentSlot::Head->position())->toBe(1)
        ->and(EquipmentSlot::Chest->position())->toBe(2)
        ->and(EquipmentSlot::MainHand->position())->toBe(3)
        ->and(EquipmentSlot::OffHand->position())->toBe(4)
        ->and(EquipmentSlot::Accessory1->position())->toBe(5)
        ->and(EquipmentSlot::Accessory2->position())->toBe(6);
});

test('accessory slot values match existing view keys', function (): void {
    expect(EquipmentSlot::Accessory1->value)->toBe('acc_1')
        ->and(EquipmentSlot::Accessory2->value)->toBe('acc_2');
});
