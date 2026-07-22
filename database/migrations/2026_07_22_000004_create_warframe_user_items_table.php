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
        Schema::create('warframe_user_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('warframe_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warframe_item_id')->nullable()->constrained('warframe_items')->nullOnDelete();
            $table->string('item_type')->index();
            $table->string('category')->index();
            $table->unsignedBigInteger('xp')->default(0);
            $table->unsignedInteger('level')->default(0);
            $table->unsignedInteger('formas')->default(0);
            $table->string('refinement')->nullable();
            $table->unsignedInteger('fusion_rank')->nullable();
            $table->unsignedInteger('max_fusion_rank')->nullable();
            $table->json('riven_stats')->nullable();
            $table->unsignedInteger('item_count')->default(1);
            $table->json('item_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warframe_user_items');
    }
};
