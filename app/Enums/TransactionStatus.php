<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * @deprecated Transactions no longer carry a status enum; `posted_at` is the
 * source of truth. This enum exists only to keep the shipped
 * `2026_07_15_100100_create_transactions_table.php` migration compilable on
 * a fresh `migrate:fresh` run. The status column was dropped in
 * `2026_07_17_100000_simplify_transactions_table.php`.
 */
enum TransactionStatus: string
{
    case Pending = 'pending';
    case Cleared = 'cleared';
    case Reconciled = 'reconciled';

    public function label(): string
    {
        return $this->value;
    }

    public function colorToken(): string
    {
        return 'muted';
    }
}
