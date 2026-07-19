<?php

declare(strict_types=1);

namespace App\Services\Budget;

use App\Models\Account;

/**
 * Aggregate-only budget snapshot for the PUBLIC view.
 *
 * No transaction detail, no account names, no payees — just totals and
 * a category breakdown. Currency is always USD; no FX conversion.
 */
final readonly class PublicBudgetSnapshot
{
    public function __construct(
        private MonthlySummaryService $summary,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function for(string $yearMonth): array
    {
        $summary = $this->summary->for($yearMonth, publicOnly: true);
        $donationAccounts = Account::query()
            ->active()
            ->where(function ($q): void {
                $q->whereNotNull('donation_url')
                    ->orWhereNotNull('donation_address')
                    ->orWhereNotNull('donation_qr_image');
            })
            ->orderBy('position')
            ->get();

        return [
            'year_month' => $summary['year_month'],
            'month_label' => $summary['month_label'],
            'display_currency' => 'USD',
            'totals' => [
                'income' => $summary['income'],
                'spent' => $summary['spent'],
                'net' => $summary['net'],
            ],
            'categories' => $summary['categories'],
            'previous_months' => $summary['previous_months'],
            'donation_accounts' => $donationAccounts,
        ];
    }
}
