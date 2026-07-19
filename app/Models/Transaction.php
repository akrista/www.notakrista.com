<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionDirection;
use Database\Factories\TransactionFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Mattiverse\Userstamps\Traits\Userstamps;
use Override;

/**
 * @property int|string|null $id
 * @property string $account_id
 * @property ?string $category_id
 * @property ?string $memo
 * @property string $amount
 * @property TransactionDirection $direction
 * @property Carbon $occurred_on
 * @property ?Carbon $posted_at
 * @property bool $is_public
 * @property ?string $payee_name
 *
 * @mixin Model
 */
#[Fillable([
    'account_id',
    'category_id',
    'memo',
    'amount',
    'direction',
    'occurred_on',
    'posted_at',
    'is_public',
    'payee_name',
])]
final class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    #[Override]
    protected $attributes = [
        'direction' => 'outflow',
        'is_public' => false,
    ];

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * @return BelongsTo<TransactionCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function markPosted(?DateTimeInterface $on = null): bool
    {
        if ($this->posted_at !== null) {
            return false;
        }

        $this->posted_at = $on instanceof DateTimeInterface
            ? Date::instance($on)
            : Date::now();

        return $this->save();
    }

    public function markUnposted(): bool
    {
        if ($this->posted_at === null) {
            return false;
        }

        $this->posted_at = null;

        return $this->save();
    }

    public function isPosted(): bool
    {
        return $this->posted_at !== null;
    }

    #[Scope]
    protected function posted(Builder $query): Builder
    {
        return $query->whereNotNull('posted_at');
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->whereNull('posted_at');
    }

    /**
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    #[Scope]
    protected function forAccount(Builder $query, string $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    #[Scope]
    protected function forCategory(Builder $query, string $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    #[Scope]
    protected function inMonth(Builder $query, string $yearMonth): Builder
    {
        $start = $yearMonth . '-01';
        $end = date('Y-m-d', strtotime($start . ' +1 month -1 day'));

        return $query->whereBetween('occurred_on', [$start, $end]);
    }

    /**
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    #[Scope]
    protected function inflows(Builder $query): Builder
    {
        return $query->where('direction', TransactionDirection::Inflow->value);
    }

    /**
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    #[Scope]
    protected function outflows(Builder $query): Builder
    {
        return $query->where('direction', TransactionDirection::Outflow->value);
    }

    /**
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    #[Scope]
    protected function public(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'direction' => TransactionDirection::class,
            'occurred_on' => 'date',
            'posted_at' => 'date',
            'is_public' => 'boolean',
        ];
    }
}
