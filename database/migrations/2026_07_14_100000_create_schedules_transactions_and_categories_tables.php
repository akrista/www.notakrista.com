<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * @var list<array{slug: string, name: string, icon: string, color_token: string, position: int}>
     */
    private const array DEFAULT_CATEGORIES = [
        ['slug' => 'payroll',           'name' => 'Payroll',                'icon' => '💼', 'color_token' => 'accent',  'position' => 1],
        ['slug' => 'rent',              'name' => 'Rent / Mortgage',        'icon' => '🏠', 'color_token' => 'red',     'position' => 2],
        ['slug' => 'utilities',         'name' => 'Utilities',              'icon' => '⚡', 'color_token' => 'red',     'position' => 3],
        ['slug' => 'internet',          'name' => 'Internet / Phone',       'icon' => '📡', 'color_token' => 'red',     'position' => 4],
        ['slug' => 'insurance',         'name' => 'Insurance',              'icon' => '🛡️', 'color_token' => 'red',     'position' => 5],
        ['slug' => 'groceries',         'name' => 'Groceries',              'icon' => '🛒', 'color_token' => 'yellow',  'position' => 6],
        ['slug' => 'eating-out',        'name' => 'Eating Out',             'icon' => '🍜', 'color_token' => 'yellow',  'position' => 7],
        ['slug' => 'transport',         'name' => 'Transport / Fuel',       'icon' => '⛽', 'color_token' => 'yellow',  'position' => 8],
        ['slug' => 'subscriptions',     'name' => 'Subscriptions',          'icon' => '🔁', 'color_token' => 'yellow',  'position' => 9],
        ['slug' => 'health',            'name' => 'Health / Medical',       'icon' => '🩺', 'color_token' => 'blue',    'position' => 10],
        ['slug' => 'repairs',           'name' => 'Repairs / Maintenance',  'icon' => '🛠️', 'color_token' => 'blue',    'position' => 11],
        ['slug' => 'tools',             'name' => 'Tools / Equipment',      'icon' => '🧰', 'color_token' => 'primary', 'position' => 12],
        ['slug' => 'earthquake-relief', 'name' => 'Earthquake Relief',      'icon' => '🇻🇪', 'color_token' => 'primary', 'position' => 13],
        ['slug' => 'personal',          'name' => 'Personal',               'icon' => '🎁', 'color_token' => 'muted',   'position' => 14],
        ['slug' => 'other',             'name' => 'Other',                  'icon' => '🏷️', 'color_token' => 'muted',   'position' => 15],
    ];

    public function up(): void
    {
        Schema::create('transaction_categories', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('icon', 16)->nullable();
            $table->string('color_token', 32)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();

            $table->index(['is_archived', 'position'], 'transaction_categories_archived_position_index');
        });

        Schema::create('schedules', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('account_id', 36);
            $table->string('category_id', 36)->nullable();
            $table->string('name');
            $table->string('payee_name')->nullable();
            $table->text('memo')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('cadence', 16);
            $table->string('direction', 16)->default('outflow');
            $table->date('next_run_on');
            $table->date('last_run_on')->nullable();
            $table->boolean('auto_post')->default(true);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();

            $table->foreign('account_id', 'schedules_account_id_foreign')
                ->references('id')->on('accounts')
                ->cascadeOnDelete();
            $table->foreign('category_id', 'schedules_category_id_foreign')
                ->references('id')->on('transaction_categories')
                ->nullOnDelete();

            $table->index(['is_active', 'auto_post', 'next_run_on'], 'schedules_due_index');
            $table->index('next_run_on', 'schedules_next_run_on_index');
        });

        Schema::create('transactions', function (Blueprint $table): void {
            $table->string('id', 36)->primary();
            $table->string('account_id', 36);
            $table->string('category_id', 36)->nullable();
            $table->text('memo')->nullable();
            $table->string('payee_name')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('direction', 16)->default('outflow');
            $table->date('occurred_on');
            $table->date('posted_at')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->userstampsUuid();
            $table->userstampsUuidSoftDeletes();

            $table->foreign('account_id', 'transactions_account_id_foreign')
                ->references('id')->on('accounts')
                ->cascadeOnDelete();
            $table->foreign('category_id', 'transactions_category_id_foreign')
                ->references('id')->on('transaction_categories')
                ->nullOnDelete();

            $table->index('occurred_on', 'transactions_occurred_on_index');
            $table->index(['account_id', 'occurred_on'], 'transactions_account_occurred_index');
        });

        $now = now();
        foreach (self::DEFAULT_CATEGORIES as $row) {
            DB::table('transaction_categories')->insert([
                'id' => (string) Str::uuid(),
                'slug' => $row['slug'],
                'name' => $row['name'],
                'icon' => $row['icon'],
                'color_token' => $row['color_token'],
                'position' => $row['position'],
                'is_archived' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['account_id']);
        });
        Schema::table('schedules', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['account_id']);
        });

        Schema::dropIfExists('transactions');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('transaction_categories');
    }
};
