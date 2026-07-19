<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TransactionDirection;
use App\Models\Account;
use App\Models\Schedule;
use App\Services\Budget\PublicBudgetSnapshot;
use Illuminate\Contracts\View\View;

final class DonationsController extends Controller
{
    public function __invoke(): View
    {
        $donationAccounts = Account::query()
            ->active()
            ->where(function ($q): void {
                $q->whereNotNull('donation_url')
                    ->orWhereNotNull('donation_address')
                    ->orWhereNotNull('donation_qr_image')
                    ->orWhereNotNull('donation_account_number')
                    ->orWhereNotNull('donation_aba')
                    ->orWhereNotNull('donation_swift')
                    ->orWhereNotNull('donation_id_cedula');
            })
            ->orderBy('position')
            ->get();

        $snapshot = resolve(PublicBudgetSnapshot::class)->for(now()->format('Y-m'));

        $actualSpent = $snapshot !== null ? (float) $snapshot['totals']['spent'] : 0.0;

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $remainingForecasted = (float) Schedule::query()
            ->active()
            ->public()
            ->where('direction', TransactionDirection::Outflow)
            ->whereBetween('next_run_on', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $targetGoal = $actualSpent + $remainingForecasted;

        return view('donations', [
            'donationAccounts' => $donationAccounts,
            'snapshot' => $snapshot,
            'targetGoal' => $targetGoal,
        ]);
    }
}
