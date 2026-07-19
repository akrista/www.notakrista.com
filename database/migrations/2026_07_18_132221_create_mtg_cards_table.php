<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mtg_cards', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('set', 10);
            $table->string('number', 10);
            $table->string('name')->nullable();
            $table->string('type_line')->nullable();
            $table->string('mana_cost')->nullable();
            $table->string('rarity')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->uuid('scryfall_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->boolean('is_sold')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();

            $table->unique(['set', 'number'], 'mtg_cards_set_number_unique');
            $table->index(['set', 'number'], 'mtg_cards_set_number_index');
        });

        $rawCards = [
            ['set' => 'aer', 'number' => '52', 'quantity' => 1],
            ['set' => 'akh', 'number' => '278', 'quantity' => 2],
            ['set' => 'akh', 'number' => '263', 'quantity' => 4],
            ['set' => 'akh', 'number' => '262', 'quantity' => 4],
            ['set' => 'akh', 'number' => '261', 'quantity' => 5],
            ['set' => 'akh', 'number' => '260', 'quantity' => 5],
            ['set' => 'akh', 'number' => '259', 'quantity' => 3],
            ['set' => 'akh', 'number' => '258', 'quantity' => 4],
            ['set' => 'akh', 'number' => '93', 'quantity' => 1],
            ['set' => 'akh', 'number' => '89', 'quantity' => 1],
            ['set' => 'akh', 'number' => '85', 'quantity' => 1],
            ['set' => 'akh', 'number' => '41', 'quantity' => 1],
            ['set' => 'akh', 'number' => '40', 'quantity' => 2],
            ['set' => 'kld', 'number' => '80', 'quantity' => 2],
            ['set' => 'kld', 'number' => '58', 'quantity' => 1],
            ['set' => 'kld', 'number' => '17', 'quantity' => 1],
            ['set' => 'w17', 'number' => '20', 'quantity' => 2],
            ['set' => 'w17', 'number' => '19', 'quantity' => 1],
            ['set' => 'w17', 'number' => '18', 'quantity' => 1],
            ['set' => 'w17', 'number' => '17', 'quantity' => 1],
            ['set' => 'w17', 'number' => '16', 'quantity' => 2],
            ['set' => 'w17', 'number' => '15', 'quantity' => 2],
            ['set' => 'w17', 'number' => '14', 'quantity' => 2],
            ['set' => 'w17', 'number' => '13', 'quantity' => 1],
            ['set' => 'w17', 'number' => '12', 'quantity' => 1],
            ['set' => 'w17', 'number' => '11', 'quantity' => 1],
            ['set' => 'w17', 'number' => '11', 'quantity' => 1],
            ['set' => 'w17', 'number' => '10', 'quantity' => 1],
            ['set' => 'w17', 'number' => '9', 'quantity' => 2],
            ['set' => 'w17', 'number' => '8', 'quantity' => 2],
            ['set' => 'w17', 'number' => '7', 'quantity' => 1],
        ];

        $aggregated = [];
        foreach ($rawCards as $card) {
            $key = sprintf('%s-%s', $card['set'], $card['number']);
            if (isset($aggregated[$key])) {
                $aggregated[$key]['quantity'] += $card['quantity'];
            } else {
                $aggregated[$key] = $card;
            }
        }

        $now = now();
        foreach ($aggregated as $card) {
            DB::table('mtg_cards')->insert([
                'id' => (string) Str::uuid(),
                'set' => $card['set'],
                'number' => $card['number'],
                'quantity' => $card['quantity'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mtg_cards');
    }
};
