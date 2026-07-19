<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('rarity');
            $table->string('icon', 16)->nullable();
            $table->string('image_url')->nullable();
            $table->json('stats')->nullable();
            $table->string('loadout')->nullable();
            $table->string('equipment_slot')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('acquired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();

            $table->index(['category', 'position'], 'items_category_position_index');
            $table->index(['loadout', 'equipment_slot'], 'items_loadout_slot_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
