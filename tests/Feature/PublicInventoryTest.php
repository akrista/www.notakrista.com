<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\WarframeAccount;
use App\Models\WarframeItem;
use App\Models\WarframeUserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public inventory route renders warframe profile and inventory data', function (): void {
    $user = User::factory()->create();

    $account = WarframeAccount::query()->create([
        'user_id' => $user->id,
        'account_name' => 'TennoPrime',
        'mastery_rank' => 30,
        'credits' => 15000000,
        'platinum' => 4500,
        'void_traces' => 1200,
        'endo' => 95000,
    ]);

    $catalogItem = WarframeItem::query()->create([
        'unique_name' => '/Lotus/Powersuits/Excalibur/Excalibur',
        'name' => 'Excalibur Prime',
        'category' => 'Warframes',
        'type' => 'Warframe',
        'image_name' => 'excalibur-prime.png',
    ]);

    WarframeUserItem::query()->create([
        'warframe_account_id' => $account->id,
        'warframe_item_id' => $catalogItem->id,
        'item_type' => '/Lotus/Powersuits/Excalibur/Excalibur',
        'category' => 'Warframe',
        'level' => 30,
        'formas' => 5,
    ]);

    $response = $this->get('/inventory');

    $response->assertOk()
        ->assertViewIs('inventory')
        ->assertViewHas('warframeData');
});
