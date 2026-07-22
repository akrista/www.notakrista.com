<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warframe_items', function (Blueprint $table): void {
            $table->id();
            $table->string('unique_name')->unique();
            $table->string('name');
            $table->string('category')->index();
            $table->string('type')->nullable();
            $table->string('relic_era')->nullable();
            $table->boolean('vaulted')->default(false);
            $table->boolean('tradeable')->default(true);
            $table->text('description')->nullable();
            $table->string('image_name')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warframe_items');
    }
};
