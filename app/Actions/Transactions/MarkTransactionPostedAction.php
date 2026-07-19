<?php

declare(strict_types=1);

namespace App\Actions\Transactions;

use App\Models\Transaction;
use DateTimeInterface;

/**
 * Stamp `posted_at = now()` on a transaction that has not yet been
 * marked posted. Idempotent: a no-op when the transaction is already posted.
 */
final readonly class MarkTransactionPostedAction
{
    public function handle(Transaction $transaction, ?DateTimeInterface $on = null): bool
    {
        return $transaction->markPosted($on);
    }
}
