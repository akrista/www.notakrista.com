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
        Schema::create('locales', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('code', 12)->unique();
            $table->string('name', 64);
            $table->string('native_name', 64);
            $table->string('direction', 3)->default('ltr');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();

            $table->index(['is_active', 'position'], 'locales_active_position_index');
        });

        $defaults = [
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'is_default' => true, 'position' => 1],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'direction' => 'ltr', 'is_default' => false, 'position' => 2],
        ];

        // `insertOrIgnore` makes the seed idempotent — re-running the
        // migration (or hitting it from a test's `migrate:fresh`) will not
        // error if `en` / `es` already exist from a prior run.
        $now = now();
        foreach ($defaults as $row) {
            DB::table('locales')->insertOrIgnore([
                'id' => (string) Str::uuid(),
                'code' => $row['code'],
                'name' => $row['name'],
                'native_name' => $row['native_name'],
                'direction' => $row['direction'],
                'is_active' => true,
                'is_default' => $row['is_default'],
                'position' => $row['position'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
