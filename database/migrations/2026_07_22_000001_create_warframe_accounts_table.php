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
        Schema::create('warframe_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_name');
            $table->string('active_avatar')->nullable();
            $table->unsignedBigInteger('credits')->default(0);
            $table->unsignedBigInteger('platinum')->default(0);
            $table->unsignedInteger('void_traces')->default(0);
            $table->unsignedInteger('endo')->default(0);
            $table->unsignedInteger('mastery_rank')->default(0);
            $table->unsignedInteger('total_warframes')->default(0);
            $table->unsignedInteger('total_weapons')->default(0);
            $table->unsignedInteger('total_mods')->default(0);
            $table->unsignedInteger('total_relics')->default(0);
            $table->json('boosters')->nullable();
            $table->timestamp('last_imported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warframe_accounts');
    }
};
