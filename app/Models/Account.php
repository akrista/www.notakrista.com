<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccountType;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Override;

/**
 * @property int|string|null $id
 * @property string $name
 * @property AccountType $type
 * @property string $currency
 * @property string $opening_balance
 * @property ?string $icon
 * @property ?string $color_token
 * @property ?string $donation_url
 * @property ?string $donation_address
 * @property ?string $donation_instructions
 * @property ?string $donation_qr_image
 * @property ?string $notes
 * @property bool $is_active
 * @property int $position
 *
 * @mixin Model
 */
#[Fillable([
    'name',
    'type',
    'currency',
    'opening_balance',
    'icon',
    'color_token',
    'donation_url',
    'donation_address',
    'donation_instructions',
    'donation_qr_image',
    'donation_account_number',
    'donation_aba',
    'donation_swift',
    'donation_id_cedula',
    'notes',
    'is_active',
    'position',
])]
final class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    #[Override]
    protected $attributes = [
        'currency' => 'USD',
        'opening_balance' => 0,
        'is_active' => true,
        'position' => 0,
    ];

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany<Schedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function hasDonationInfo(): bool
    {
        return ($this->donation_url !== null && $this->donation_url !== '')
            || ($this->donation_address !== null && $this->donation_address !== '')
            || ($this->donation_qr_image !== null && $this->donation_qr_image !== '')
            || ($this->donation_account_number !== null && $this->donation_account_number !== '')
            || ($this->donation_aba !== null && $this->donation_aba !== '')
            || ($this->donation_swift !== null && $this->donation_swift !== '')
            || ($this->donation_id_cedula !== null && $this->donation_id_cedula !== '');
    }

    /**
     * @param  Builder<Account>  $query
     * @return Builder<Account>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
            'opening_balance' => 'decimal:2',
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }
}
