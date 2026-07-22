<?php

declare(strict_types=1);

use App\Enums\EquipmentSlot;
use App\Enums\ItemLoadout;
use App\Models\Category;
use App\Models\IndependenciaCard;
use App\Models\Item;
use App\Models\MtgCard;
use App\Models\YugiohCard;
use Illuminate\Support\Facades\Http;

test('inventory page loads with items and selected item id', function (): void {
    Item::factory()->create([
        'slug' => 'phpstorm',
        'name_en' => 'PhpStorm IDE',
        'name_es' => 'PhpStorm IDE',
        'desc_en' => 'PHP IDE.',
        'desc_es' => 'IDE de PHP.',
        'type_en' => 'IDE',
        'type_es' => 'IDE',
    ]);

    $response = $this->get(route('inventory'));

    $response->assertOk()
        ->assertSee('PhpStorm IDE')
        ->assertSee('"phpstorm"', escape: false);
});

test('inventory page returns empty when no items exist', function (): void {
    $response = $this->get(route('inventory'));

    $response->assertOk();
});

test('inventory page orders items by category position then item position', function (): void {
    $book = Category::query()->where('slug', 'book')->firstOrFail();
    $tech = Category::query()->where('slug', 'tech')->firstOrFail();

    Item::factory()->create(['slug' => 'first',  'category_id' => $tech->getKey(), 'position' => 2]);
    Item::factory()->create(['slug' => 'second', 'category_id' => $tech->getKey(), 'position' => 1]);
    Item::factory()->create(['slug' => 'third',  'category_id' => $book->getKey(), 'position' => 5]);

    $response = $this->get(route('inventory'));

    // tech (pos 1) comes first, then book (pos 3); within tech: second (pos 1), then first (pos 2)
    $response->assertOk()
        ->assertSeeInOrder(['"second"', '"first"', '"third"']);
});

test('character page groups equipped items by composite slot key', function (): void {
    Item::factory()->equipped(ItemLoadout::Ranked, EquipmentSlot::Head)->create([
        'name_en' => 'Shield of DevOps',
        'name_es' => 'Escudo DevOps',
        'desc_en' => 'd',
        'desc_es' => 'd',
        'type_en' => 't',
        'type_es' => 't',
    ]);

    $response = $this->get(route('character'));

    $response->assertOk()
        ->assertSee('"ranked_head"', escape: false)
        ->assertSee('Shield of DevOps');
});

test('inventory page displays mtg and yugioh cards but excludes independencia cards', function (): void {
    Http::fake([
        'api.scryfall.com/cards/*' => Http::response([
            'id' => 'scry-1',
            'name' => 'Black Lotus',
            'set' => 'lea',
            'collector_number' => '232',
            'type_line' => 'Artifact',
            'mana_cost' => '{0}',
            'rarity' => 'rare',
            'prices' => ['usd' => '20000.00'],
            'image_uris' => ['normal' => 'https://cards.scryfall.io/black-lotus.jpg'],
        ], 200),
        'db.ygoprodeck.com/api/*' => Http::response([
            'name' => 'Blue-Eyes White Dragon',
            'set_rarity' => 'Ultra Rare',
            'set_price' => '50.00',
            'id' => 89631139,
            'type' => 'Normal Monster',
            'humanReadableCardType' => 'Normal Monster',
            'frameType' => 'normal',
            'card_images' => [
                ['image_url' => 'https://images.ygoprodeck.com/blue-eyes.jpg'],
            ],
            'card_prices' => [
                ['tcgplayer_price' => '45.00'],
            ],
        ], 200),
    ]);

    // Create MTG card
    MtgCard::factory()->create([
        'name' => 'Black Lotus',
        'set' => 'lea',
        'number' => '232',
    ]);

    // Create Yu-Gi-Oh! card
    YugiohCard::factory()->create([
        'name' => 'Blue-Eyes White Dragon',
        'setcode' => 'LOB-001',
    ]);

    // Create Independencia card
    IndependenciaCard::factory()->create([
        'name' => 'Excludeme Card',
    ]);

    $response = $this->get(route('inventory'));

    $response->assertOk()
        ->assertSee('Black Lotus')
        ->assertSee('Blue-Eyes White Dragon')
        ->assertDontSee('Excludeme Card');
});

test('inventory page includes formatted mana cost html for mtg cards', function (): void {
    MtgCard::factory()->create([
        'name' => 'Counterspell',
        'mana_cost' => '{U}{U}',
    ]);

    $response = $this->get(route('inventory'));

    $response->assertOk()
        ->assertSee('Counterspell')
        ->assertSee('background-color: #c1d7e9', escape: false);
});

test('inventory page renders yugioh collection summary and toolbar controls', function (): void {
    Http::fake([
        'https://db.ygoprodeck.com/api/v7/cardsetsinfo.php*' => Http::response([
            'name' => 'Dark Magician',
            'set_rarity' => 'Ultra Rare',
            'set_price' => '15.50',
            'id' => 46986414,
        ]),
        'https://db.ygoprodeck.com/api/v7/cardinfo.php*' => Http::response([
            'data' => [
                [
                    'type' => 'Spellcaster / Normal',
                    'frameType' => 'normal',
                    'card_prices' => [['tcgplayer_price' => '15.50']],
                    'card_images' => [['image_url' => 'https://via.placeholder.com/640x480.png']],
                ],
            ],
        ]),
    ]);

    YugiohCard::factory()->create([
        'name' => 'Dark Magician',
        'setcode' => 'SDY-006',
        'rarity' => 'Ultra Rare',
        'type' => 'Spellcaster / Normal',
        'quantity' => 2,
        'card_price' => 15.50,
        'ygoprodeck_id' => 46986414,
    ]);

    $response = $this->get(route('inventory'));

    $response->assertOk()
        ->assertSee('Dark Magician')
        ->assertSee('SDY-006')
        ->assertSee('ygoprodeck_id', escape: false)
        ->assertSee('46986414');
});

test('inventory page renders YGO tab before MTG tab', function (): void {
    $response = $this->get(route('inventory'));

    $response->assertOk()
        ->assertSeeInOrder(['Yu-Gi-Oh!', 'Magic: The Gathering']);
});
