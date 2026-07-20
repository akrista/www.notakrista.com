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
        Schema::create('categories', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('slug')->unique();
            $table->string('icon', 16)->nullable();
            $table->string('color_token', 32)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();
        });

        $defaults = [
            ['slug' => 'tech',         'icon' => '💻', 'color_token' => 'blue',    'position' => 1,  'en' => 'Tech',         'es' => 'Tecnología'],
            ['slug' => 'clothing',     'icon' => '👕', 'color_token' => 'accent',  'position' => 2,  'en' => 'Clothing',     'es' => 'Ropa'],
            ['slug' => 'book',         'icon' => '📚', 'color_token' => 'primary', 'position' => 3,  'en' => 'Book',         'es' => 'Libro'],
            ['slug' => 'tool',         'icon' => '🔧', 'color_token' => 'muted',   'position' => 4,  'en' => 'Tool',         'es' => 'Herramienta'],
            ['slug' => 'kitchenware',  'icon' => '☕', 'color_token' => 'yellow',  'position' => 5,  'en' => 'Kitchenware',  'es' => 'Cocina'],
            ['slug' => 'stationery',   'icon' => '✏️', 'color_token' => 'primary', 'position' => 6,  'en' => 'Stationery',   'es' => 'Papelería'],
            ['slug' => 'misc',         'icon' => '📦', 'color_token' => 'muted',   'position' => 99, 'en' => 'Misc',         'es' => 'Otros'],
        ];

        $now = now();
        $seeded = [];
        foreach ($defaults as $row) {
            $seeded[$row['slug']] = (string) Str::uuid();
            DB::table('categories')->insert([
                'id' => $seeded[$row['slug']],
                'slug' => $row['slug'],
                'icon' => $row['icon'],
                'color_token' => $row['color_token'],
                'position' => $row['position'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('language_lines')->insert([
                'group' => 'categories',
                'key' => $row['slug'],
                'text' => json_encode([
                    'en' => $row['en'],
                    'es' => $row['es'],
                ], JSON_THROW_ON_ERROR),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasColumn('items', 'category')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->dropIndex('items_category_position_index');
            });
        }

        Schema::table('items', function (Blueprint $table): void {
            $table->string('category_id', 36)->nullable()->after('category');
        });

        if (Schema::hasColumn('items', 'category')) {
            $cases = [];
            $bindings = [];
            foreach ($seeded as $slug => $id) {
                $cases[] = 'WHEN ? THEN ?';
                $bindings[] = $slug;
                $bindings[] = $id;
            }

            $caseSql = implode(' ', $cases);

            $castType = 'varchar';
            DB::statement(
                sprintf('UPDATE items SET category_id = CAST(CASE category %s END AS %s) WHERE category IS NOT NULL', $caseSql, $castType),
                $bindings
            );
        }

        Schema::table('items', function (Blueprint $table): void {
            $table->foreign('category_id', 'items_category_id_foreign')
                ->references('id')->on('categories')
                ->nullOnDelete();
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->dropColumn('category');
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->index(['category_id', 'position'], 'items_category_id_position_index');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropIndex('items_category_id_position_index');
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('rarity');
        });

        DB::statement('
            UPDATE items SET category = (
                SELECT slug FROM categories WHERE categories.id = items.category_id
            )
            WHERE category_id IS NOT NULL
        ');

        Schema::table('items', function (Blueprint $table): void {
            $table->dropForeign('items_category_id_foreign');
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->dropColumn('category_id');
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->index(['category', 'position'], 'items_category_position_index');
        });

        Schema::dropIfExists('categories');
        DB::table('language_lines')->where('group', 'categories')->delete();
    }
};
