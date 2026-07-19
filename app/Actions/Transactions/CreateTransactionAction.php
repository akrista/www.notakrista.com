<?php

declare(strict_types=1);

namespace App\Actions\Transactions;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Carbon;

/**
 * @phpstan-type TransactionInput array{
 *     account_id: string,
 *     amount: float|int|string,
 *     direction: string|TransactionDirection,
 *     occurred_on: string|Carbon,
 *     category_id?: ?string,
 *     payee_name?: ?string,
 *     memo?: ?string,
 *     is_public?: bool,
 *     posted_at?: string|Carbon|null
 * }
 */
final readonly class CreateTransactionAction
{
    /**
     * @param  TransactionInput  $data
     */
    public function handle(array $data): Transaction
    {
        $account = Account::query()->findOrFail($data['account_id']);

        $direction = $data['direction'] instanceof TransactionDirection
            ? $data['direction']
            : TransactionDirection::from((string) $data['direction']);

        $occurredOn = $data['occurred_on'] instanceof Carbon
            ? $data['occurred_on']->toDateString()
            : (string) $data['occurred_on'];

        $postedAt = null;
        if (isset($data['posted_at']) && $data['posted_at'] !== null) {
            $postedAt = $data['posted_at'] instanceof Carbon
                ? $data['posted_at']->toDateString()
                : (string) $data['posted_at'];
        }

        $transaction = new Transaction([
            'account_id' => $account->getKey(),
            'category_id' => $data['category_id'] ?? null,
            'payee_name' => $data['payee_name'] ?? null,
            'memo' => $data['memo'] ?? null,
            'amount' => round((float) $data['amount'], 2),
            'direction' => $direction,
            'occurred_on' => $occurredOn,
            'posted_at' => $postedAt,
            'is_public' => (bool) ($data['is_public'] ?? false),
        ]);

        $transaction->save();

        return $transaction->refresh();
    }
}
