<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('name');
            $table->string('type');
            $table->string('currency', 10)->default('USD');
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->string('icon', 16)->nullable();
            $table->string('color_token', 32)->nullable();
            $table->string('donation_url')->nullable();
            $table->text('donation_address')->nullable();
            $table->string('donation_account_number')->nullable();
            $table->string('donation_aba')->nullable();
            $table->string('donation_swift')->nullable();
            $table->string('donation_id_cedula')->nullable();
            $table->text('donation_instructions')->nullable();
            $table->string('donation_qr_image')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();

            $table->index(['is_active', 'position'], 'accounts_active_position_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
