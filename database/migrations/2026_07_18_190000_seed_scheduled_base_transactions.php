<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * @var list<array{account_name: string, category_slug: string, name: string, payee_name: string, amount: float, cadence: string, direction: string, next_run_on: string}>
     */
    private const array SCHEDULES = [
        [
            'account_name' => 'Facebank (Puerto Rico)',
            'category_slug' => 'subscriptions',
            'name' => 'YouTube Premium Subscription',
            'payee_name' => 'YouTube',
            'amount' => 18.09,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-18',
        ],
        [
            'account_name' => 'Facebank (Puerto Rico)',
            'category_slug' => 'subscriptions',
            'name' => 'YouTube Oleg Member Subscription',
            'payee_name' => 'YouTube',
            'amount' => 4.99,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-01',
        ],
        [
            'account_name' => 'Facebank (Puerto Rico)',
            'category_slug' => 'other',
            'name' => 'Facebank Account Maintenance',
            'payee_name' => 'Facebank',
            'amount' => 6.00,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-01',
        ],
        [
            'account_name' => 'Facebank (Puerto Rico)',
            'category_slug' => 'health',
            'name' => 'Psychoanalist Consultation',
            'payee_name' => 'Psychoanalist',
            'amount' => 25.00,
            'cadence' => 'biweekly',
            'direction' => 'outflow',
            'next_run_on' => '2026-07-20',
        ],
        [
            'account_name' => 'Facebank (Puerto Rico)',
            'category_slug' => 'insurance',
            'name' => 'Mercantil Seguros Panama Insurance Policy',
            'payee_name' => 'Mercantil Seguros Panama',
            'amount' => 241.13,
            'cadence' => 'quarterly',
            'direction' => 'outflow',
            'next_run_on' => '2026-10-01',
        ],
        [
            'account_name' => 'Facebank (Puerto Rico)',
            'category_slug' => 'payroll',
            'name' => 'TBTB Global Job Payment',
            'payee_name' => 'TBTB Global',
            'amount' => 900.00,
            'cadence' => 'monthly',
            'direction' => 'inflow',
            'next_run_on' => '2026-07-30',
        ],
        [
            'account_name' => 'Facebank (Puerto Rico)',
            'category_slug' => 'internet',
            'name' => 'MiOrange Spain Mobile Recharge',
            'payee_name' => 'MiOrange',
            'amount' => 23.29,
            'cadence' => 'bimonthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-01',
        ],
        [
            'account_name' => 'Bancamiga',
            'category_slug' => 'insurance',
            'name' => 'Cancer Insurance Policy',
            'payee_name' => 'Bancamiga',
            'amount' => 5.00,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-01',
        ],
        [
            'account_name' => 'Bancamiga',
            'category_slug' => 'internet',
            'name' => 'Wow Catelca Internet Service',
            'payee_name' => 'Wow Catelca',
            'amount' => 30.00,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-16',
        ],
        [
            'account_name' => 'Zinli',
            'category_slug' => 'subscriptions',
            'name' => 'Opencode (Anomalyco) Service',
            'payee_name' => 'Opencode (Anomalyco)',
            'amount' => 10.00,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-07-24',
        ],
        [
            'account_name' => 'Zinli',
            'category_slug' => 'other',
            'name' => 'Zinli Account Maintenance Tax',
            'payee_name' => 'Zinli',
            'amount' => 0.99,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-01',
        ],
        [
            'account_name' => 'Zinli',
            'category_slug' => 'subscriptions',
            'name' => 'Contabo VPS Germany Monthly Debt',
            'payee_name' => 'Contabo',
            'amount' => 17.25,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-07-24',
        ],
        [
            'account_name' => 'Bancamiga',
            'category_slug' => 'internet',
            'name' => 'CANTV Internet Service',
            'payee_name' => 'CANTV',
            'amount' => 20.00,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-18',
        ],
        [
            'account_name' => 'PayPal',
            'category_slug' => 'subscriptions',
            'name' => 'GitHub Pro Subscription',
            'payee_name' => 'GitHub',
            'amount' => 4.00,
            'cadence' => 'monthly',
            'direction' => 'outflow',
            'next_run_on' => '2026-08-01',
        ],
    ];

    public function up(): void
    {
        $now = now();

        // 1. Ensure Zinli account exists in database.
        $zinliExists = DB::table('accounts')->where('name', 'Zinli')->exists();
        if (! $zinliExists) {
            DB::table('accounts')->insert([
                'id' => (string) Str::uuid(),
                'name' => 'Zinli',
                'type' => 'wallet',
                'currency' => 'USD',
                'icon' => '💳',
                'color_token' => 'primary',
                'position' => 6,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Insert schedules.
        foreach (self::SCHEDULES as $row) {
            $accountId = DB::table('accounts')->where('name', $row['account_name'])->value('id');
            $categoryId = DB::table('transaction_categories')->where('slug', $row['category_slug'])->value('id');

            if (! $accountId) {
                continue;
            }

            // Check if this schedule is already seeded to avoid duplicates.
            $exists = DB::table('schedules')
                ->where('account_id', $accountId)
                ->where('name', $row['name'])
                ->where('amount', $row['amount'])
                ->exists();

            if (! $exists) {
                DB::table('schedules')->insert([
                    'id' => (string) Str::uuid(),
                    'account_id' => $accountId,
                    'category_id' => $categoryId,
                    'name' => $row['name'],
                    'payee_name' => $row['payee_name'],
                    'memo' => null,
                    'amount' => $row['amount'],
                    'cadence' => $row['cadence'],
                    'direction' => $row['direction'],
                    'next_run_on' => $row['next_run_on'],
                    'last_run_on' => null,
                    'auto_post' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Remove seeded schedules.
        foreach (self::SCHEDULES as $row) {
            $accountId = DB::table('accounts')->where('name', $row['account_name'])->value('id');
            if ($accountId) {
                DB::table('schedules')
                    ->where('account_id', $accountId)
                    ->where('name', $row['name'])
                    ->where('amount', $row['amount'])
                    ->delete();
            }
        }
    }
};
