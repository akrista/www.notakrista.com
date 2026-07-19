<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * @var list<array{name: string, type: string, currency: string, icon: string, color_token: string, position: int, donation_url: ?string, donation_address: ?string, donation_account_number: ?string, donation_aba: ?string, donation_swift: ?string, donation_id_cedula: ?string, donation_instructions: ?string, donation_qr_image: ?string}>
     */
    private const array DEFAULTS = [
        [
            'name' => 'Facebank (Puerto Rico)',
            'type' => 'bank',
            'currency' => 'USD',
            'icon' => '🏦',
            'color_token' => 'blue',
            'position' => 1,
            'donation_url' => null,
            'donation_address' => "Account: 27040002774\nABA: 021502189\nSWIFT: FILCPR22",
            'donation_account_number' => '27040002774',
            'donation_aba' => '021502189',
            'donation_swift' => 'FILCPR22',
            'donation_id_cedula' => null,
            'donation_instructions' => 'United States bank transfer. PR-based correspondent bank.',
            'donation_qr_image' => null,
        ],
        [
            'name' => 'PayPal',
            'type' => 'wallet',
            'currency' => 'USD',
            'icon' => 'P',
            'color_token' => 'primary',
            'position' => 3,
            'donation_url' => 'https://paypal.me/akristax',
            'donation_address' => 'paypal.me/akristax',
            'donation_account_number' => null,
            'donation_aba' => null,
            'donation_swift' => null,
            'donation_id_cedula' => null,
            'donation_instructions' => 'Use the Friends & Family option to avoid fees, or Goods & Services for buyer protection.',
            'donation_qr_image' => null,
        ],
        [
            'name' => 'Binance',
            'type' => 'exchange',
            'currency' => 'USDT',
            'icon' => '₿',
            'color_token' => 'yellow',
            'position' => 2,
            'donation_url' => 'https://app.binance.com/uni-qr/YXX86CKj',
            'donation_address' => 'https://app.binance.com/uni-qr/YXX86CKj',
            'donation_account_number' => null,
            'donation_aba' => null,
            'donation_swift' => null,
            'donation_id_cedula' => null,
            'donation_instructions' => 'Scan the QR with the Binance app, or use the Binance Pay ID "akrista" or "39193465".',
            'donation_qr_image' => 'images/donations/binance-pay-qr.jpg',
        ],
        [
            'name' => 'Bancamiga',
            'type' => 'bank',
            'currency' => 'VES',
            'icon' => '🏦',
            'color_token' => 'accent',
            'position' => 4,
            'donation_url' => null,
            'donation_address' => "Account: 01720111511118373305\nID: V-22438686",
            'donation_account_number' => '01720111511118373305',
            'donation_aba' => null,
            'donation_swift' => null,
            'donation_id_cedula' => 'V-22438686',
            'donation_instructions' => 'Cuenta en bolívares. Transferencia o depósito. Pago Móvil disponible al 04142034875, CI: 22438686.',
            'donation_qr_image' => 'images/donations/bancamiga-pagomovil-qr.jpg',
        ],
        [
            'name' => 'BDV',
            'type' => 'bank',
            'currency' => 'VES',
            'icon' => '🏦',
            'color_token' => 'blue',
            'position' => 5,
            'donation_url' => null,
            'donation_address' => "Account: 01020107150000144571\nID: V-22438686",
            'donation_account_number' => '01020107150000144571',
            'donation_aba' => null,
            'donation_swift' => null,
            'donation_id_cedula' => 'V-22438686',
            'donation_instructions' => 'Banco de Venezuela, cuenta en bolívares. Pago Móvil disponible al 04142034875, CI: 22438686.',
            'donation_qr_image' => null,
        ],
        [
            'name' => 'Zinli',
            'type' => 'wallet',
            'currency' => 'USD',
            'icon' => '💳',
            'color_token' => 'primary',
            'position' => 6,
            'donation_url' => null,
            'donation_address' => null,
            'donation_account_number' => null,
            'donation_aba' => null,
            'donation_swift' => null,
            'donation_id_cedula' => null,
            'donation_instructions' => null,
            'donation_qr_image' => null,
        ],
        [
            'name' => 'Cash',
            'type' => 'cash',
            'currency' => 'USD',
            'icon' => '💵',
            'color_token' => 'accent',
            'position' => 7,
            'donation_url' => null,
            'donation_address' => null,
            'donation_account_number' => null,
            'donation_aba' => null,
            'donation_swift' => null,
            'donation_id_cedula' => null,
            'donation_instructions' => null,
            'donation_qr_image' => null,
        ],
    ];

    public function up(): void
    {
        $now = now();

        foreach (self::DEFAULTS as $row) {
            $existing = DB::table('accounts')->where('name', $row['name'])->first();

            if ($existing !== null) {
                DB::table('accounts')
                    ->where('id', $existing->id)
                    ->update(array_merge($row, [
                        'updated_at' => $now,
                    ]));
            } else {
                DB::table('accounts')->insert(array_merge($row, [
                    'id' => (string) Str::uuid(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('accounts')
            ->whereIn('name', array_column(self::DEFAULTS, 'name'))
            ->delete();
    }
};
