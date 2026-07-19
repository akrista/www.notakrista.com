<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BillCadence;
use App\Enums\TransactionDirection;
use Database\Factories\ScheduleFactory;
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
 * @property string $name
 * @property string $account_id
 * @property ?string $category_id
 * @property ?string $payee_name
 * @property ?string $memo
 * @property string $amount
 * @property BillCadence $cadence
 * @property TransactionDirection $direction
 * @property Carbon $next_run_on
 * @property ?Carbon $last_run_on
 * @property bool $auto_post
 * @property bool $is_active
 *
 * @mixin Model
 */
#[Fillable([
    'name',
    'account_id',
    'category_id',
    'payee_name',
    'memo',
    'amount',
    'cadence',
    'direction',
    'next_run_on',
    'last_run_on',
    'auto_post',
    'is_public',
    'is_active',
])]
final class Schedule extends Model
{
    /** @use HasFactory<ScheduleFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    #[Override]
    protected $attributes = [
        'direction' => 'outflow',
        'auto_post' => true,
        'is_public' => false,
        'is_active' => true,
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

    /**
     * Advance the schedule by one cadence interval, stamping the
     * previous `next_run_on` into `last_run_on`.
     */
    public function markRan(?DateTimeInterface $on = null): bool
    {
        $base = $this->next_run_on instanceof DateTimeInterface
            ? $this->next_run_on
            : (\is_string($this->next_run_on) ? Date::parse($this->next_run_on) : null);

        $this->last_run_on = ($on ?? now())->format('Y-m-d');
        $this->next_run_on = $base instanceof DateTimeInterface
            ? $this->cadence->advance($base)->format('Y-m-d')
            : null;

        return $this->save();
    }

    public function isDue(?DateTimeInterface $on = null): bool
    {
        $now = ($on ?? now())->format('Y-m-d');
        $next = $this->formatDate($this->next_run_on);

        return $next <= $now;
    }

    /**
     * @param  Builder<Schedule>  $query
     * @return Builder<Schedule>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Schedule>  $query
     * @return Builder<Schedule>
     */
    #[Scope]
    protected function public(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * @param  Builder<Schedule>  $query
     * @return Builder<Schedule>
     */
    #[Scope]
    protected function dueOnOrBefore(Builder $query, DateTimeInterface | string $date): Builder
    {
        $value = $date instanceof DateTimeInterface
            ? $date->format('Y-m-d')
            : (string) $date;

        return $query->whereDate('next_run_on', '<=', $value);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'cadence' => BillCadence::class,
            'direction' => TransactionDirection::class,
            'next_run_on' => 'date',
            'last_run_on' => 'date',
            'auto_post' => 'boolean',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    private function formatDate(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return is_string($value) ? mb_substr($value, 0, 10) : '';
    }
}
