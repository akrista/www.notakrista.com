<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<array{key: string, en: string, es: string}>
     */
    private const array HOME_PHRASE_SEEDS = [
        ['key' => 'from_caracas',         'en' => 'from Caracas',                       'es' => 'caraqueño'],
        ['key' => 'gamer_since_93',       'en' => "gaming since '93",                  'es' => 'gamer desde el 93'],
        ['key' => 'running_linux',        'en' => 'running on Linux',                   'es' => 'corriendo en Linux'],
        ['key' => 'living_docker',        'en' => 'living in Docker',                   'es' => 'viviendo en Docker'],
        ['key' => 'ssh_enjoyer',          'en' => 'and an SSH enjoyer',                 'es' => 'amante de SSH'],
        ['key' => 'sqlite_when_possible', 'en' => 'SQLite when possible',               'es' => 'SQLite cuando puedo'],
        ['key' => 'postgres_fan',         'en' => 'and a Postgres fan',                 'es' => 'fan de Postgres'],
        ['key' => 'dba_heart',            'en' => 'and a DBA at heart',                 'es' => 'DBA de corazón'],
        ['key' => 'shipping_public',      'en' => 'shipping in public',                 'es' => 'commiteando en vivo'],
        ['key' => 'building_bizkit',      'en' => 'building BizKit',                    'es' => 'construyendo BizKit'],
        ['key' => 'writing_migrations',   'en' => 'writing migrations',                 'es' => 'escribiendo migraciones'],
        ['key' => 'tf2_loyalist',         'en' => 'and a TF2 loyalist',                 'es' => 'fiel a TF2'],
        ['key' => 'quake_veteran',        'en' => 'and a Quake veteran',                'es' => 'veterano de Quake'],
        ['key' => 'warframe_loki',        'en' => 'and a main Loki in Warframe',        'es' => 'main Loki en Warframe'],
        ['key' => 'warframe_translator',  'en' => 'and a Warframe translator',          'es' => 'y traductor de Warframe'],
        ['key' => 'nin_diehard',          'en' => 'and a NIN diehard',                  'es' => 'fanático de NIN'],
        ['key' => 'limp_bizkit_fan',      'en' => 'and a Limp Bizkit fan',              'es' => 'fan de Limp Bizkit'],
        ['key' => 'free_market_fan',      'en' => 'and a free market fan',              'es' => 'fan del libre mercado'],
        ['key' => 'socialism_survivor',   'en' => 'and a socialism survivor',           'es' => 'sobreviviente del socialismo'],
    ];

    public function up(): void
    {
        Schema::create('language_lines', function (Blueprint $table): void {
            $table->id();
            $table->string('group')->index();
            $table->string('key');
            $table->json('text');
            $table->boolean('is_active')->default(true)->after('text');
            $table->timestamps();

            $table->index(['group', 'is_active'], 'language_lines_group_active_index');
        });

        $now = now();
        foreach (self::HOME_PHRASE_SEEDS as $phrase) {
            DB::table('language_lines')->insert([
                'group' => 'home_phrases',
                'key' => $phrase['key'],
                'text' => json_encode([
                    'en' => $phrase['en'],
                    'es' => $phrase['es'],
                ], JSON_THROW_ON_ERROR),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('language_lines');
    }
};
