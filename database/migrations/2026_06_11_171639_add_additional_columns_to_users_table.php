<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('name');
            $table->string('username')->unique()->after('id');
            $table->string('firstname')->after('username');
            $table->string('lastname')->after('firstname');
            $table->string('avatar_url')->after('remember_token')->nullable();
            $table->text('filament_authentication_secret')->after('avatar_url')->nullable();
            $table->text('filament_authentication_recovery_codes')->after('filament_authentication_secret')->nullable();
            $table->boolean('has_email_authentication')->after('filament_authentication_recovery_codes')->default(false);
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();
            $table->index(['email'], 'users_email_index');
            $table->index(['username'], 'users_username_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('name');
            $table->dropColumn('username');
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
            $table->dropColumn('avatar_url');
            $table->dropColumn('filament_authentication_secret');
            $table->dropColumn('filament_authentication_recovery_codes');
            $table->dropColumn('has_email_authentication');
            $table->dropSoftDeletes();
            $table->dropUserstampsUuid();
            $table->dropUserstampsUuidSoftDeletes();
            $table->dropIndex('users_email_index');
            $table->dropIndex('users_username_index');
        });
    }
};
