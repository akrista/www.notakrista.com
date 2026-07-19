<?php

declare(strict_types=1);

namespace App\Actions\Schedules;

use App\Actions\Transactions\CreateTransactionAction;
use App\Enums\TransactionDirection;
use App\Models\Schedule;
use App\Models\Transaction;
use DateTimeInterface;
use Illuminate\Support\Facades\Date;

/**
 * Post a schedule: creates a Transaction from the schedule template
 * using the schedule's next_run_on as the occurred_on date, stamps
 * `posted_at = now()` when the schedule is `auto_post`, and then
 * advances the schedule by one cadence interval.
 */
final readonly class PostScheduleAction
{
    public function __construct(
        private CreateTransactionAction $createTransaction,
    ) {}

    public function handle(Schedule $schedule, ?DateTimeInterface $on = null): ?Transaction
    {
        if (! $schedule->is_active) {
            return null;
        }

        $occurredOn = ($on ?? Date::now())->toDateString();
        $postNow = $schedule->auto_post;

        $transaction = $this->createTransaction->handle([
            'account_id' => $schedule->account_id,
            'category_id' => $schedule->category_id,
            'amount' => (float) $schedule->amount,
            'direction' => $schedule->direction instanceof TransactionDirection
                ? $schedule->direction
                : TransactionDirection::Outflow,
            'occurred_on' => $occurredOn,
            'posted_at' => $postNow ? ($on ?? Date::now()) : null,
            'is_public' => $schedule->is_public,
            'memo' => $schedule->memo,
            'payee_name' => $schedule->payee_name,
        ]);

        $schedule->markRan($on ?? Date::now());

        return $transaction;
    }
}
