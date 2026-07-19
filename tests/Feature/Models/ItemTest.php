<?php

declare(strict_types=1);

use App\Enums\EquipmentSlot;
use App\Enums\ItemLoadout;
use App\Enums\ItemRarity;
use App\Models\Category;
use App\Models\Item;
use App\Models\LanguageLine;

test('uses uuid primary keys', function (): void {
    $item = Item::factory()->create();

    expect($item->id)->toBeString()
        ->and(mb_strlen((string) $item->id))->toBe(36);
});

test('casts enum columns', function (): void {
    $category = Category::query()->where('slug', 'tech')->firstOrFail();
    $item = Item::factory()
        ->equipped(ItemLoadout::Ranked, EquipmentSlot::MainHand)
        ->rarity(ItemRarity::Legendary)
        ->forCategory($category)
        ->create();

    expect($item->category_id)->toBe($category->getKey())
        ->and($item->rarity)->toBe(ItemRarity::Legendary)
        ->and($item->loadout)->toBe(ItemLoadout::Ranked)
        ->and($item->equipment_slot)->toBe(EquipmentSlot::MainHand)
        ->and($item->stats)->toBeArray()
        ->and($item->category)->toBeInstanceOf(Category::class);
});

test('isEquipped returns true only when both loadout and slot are set', function (): void {
    $equipped = Item::factory()->equipped(ItemLoadout::Casual, EquipmentSlot::Head)->create();
    $bag = Item::factory()->create(['loadout' => null, 'equipment_slot' => null]);

    expect($equipped->isEquipped())->toBeTrue()
        ->and($bag->isEquipped())->toBeFalse();
});

test('translation helpers fall back to english when locale is missing', function (): void {
    $item = Item::factory()->create([
        'name_en' => 'PhpStorm',
        'name_es' => 'PhpStorm',
        'type_en' => 'IDE',
        'type_es' => '',
        'desc_en' => 'A PHP IDE.',
        'desc_es' => '',
    ]);

    expect($item->name('es'))->toBe('PhpStorm')
        ->and($item->description('es'))->toBe('A PHP IDE.')
        ->and($item->typeLabel('es'))->toBe('IDE')
        ->and($item->name('de'))->toBe('PhpStorm');
});

test('forLoadout scope filters and orders by slot position', function (): void {
    Item::factory()->equipped(ItemLoadout::Ranked, EquipmentSlot::Chest)->create();
    Item::factory()->equipped(ItemLoadout::Ranked, EquipmentSlot::Head)->create();
    Item::factory()->equipped(ItemLoadout::Casual, EquipmentSlot::Head)->create();
    Item::factory()->create(['loadout' => null, 'equipment_slot' => null]);

    $ranked = Item::query()->forLoadout(ItemLoadout::Ranked)->get();

    expect($ranked)->toHaveCount(2)
        ->and($ranked->first()->equipment_slot)->toBe(EquipmentSlot::Head);
});

test('soft deletes work and keep rows in the table', function (): void {
    $item = Item::factory()->create();
    $item->delete();

    expect(Item::query()->count())->toBe(0)
        ->and(Item::withTrashed()->count())->toBe(1);
});

test('factory creates language lines for each translatable field', function (): void {
    $item = Item::factory()->create();

    foreach (['name', 'type', 'desc'] as $field) {
        $line = LanguageLine::query()
            ->where('group', LanguageLine::ITEMS_GROUP)
            ->where('key', sprintf('%s.%s', $item->slug, $field))
            ->first();

        expect($line)->not->toBeNull();
    }
});
