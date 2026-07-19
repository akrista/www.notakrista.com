<?php

declare(strict_types=1);

namespace App\Services\Budget;

use App\Enums\TransactionDirection;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

/**
 * Compute monthly aggregate-only budget data.
 *
 * Used by both the admin dashboard (all transactions) and the public
 * transparency page (`is_public = true` only).
 */
final readonly class MonthlySummaryService
{
    /**
     * @return array{
     *     year_month: string,
     *     month_label: string,
     *     income: float,
     *     spent: float,
     *     net: float,
     *     transaction_count: int,
     *     categories: Collection<int, array{slug: string, name: string, icon: ?string, color_token: ?string, spent: float, count: int}>,
     *     previous_months: list<array{year_month: string, label: string, income: float, spent: float}>
     * }
     */
    public function for(string $yearMonth, bool $publicOnly = false): array
    {
        $base = Transaction::query();

        if ($publicOnly) {
            $base->public();
        }

        $rows = (clone $base)
            ->inMonth($yearMonth)
            ->get();

        $income = (float) $rows->where('direction', TransactionDirection::Inflow)->sum('amount');
        $spent = (float) $rows->where('direction', TransactionDirection::Outflow)->sum('amount');

        $categoryIds = $rows
            ->where('direction', TransactionDirection::Outflow)
            ->pluck('category_id')
            ->filter()
            ->unique()
            ->values();

        $categories = TransactionCategory::query()
            ->whereIn('id', $categoryIds)
            ->orderBy('position')
            ->get()
            ->map(function (TransactionCategory $category) use ($rows): array {
                $matched = $rows->where('category_id', $category->getKey());

                return [
                    'slug' => $category->slug,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'color_token' => $category->color_token,
                    'spent' => (float) $matched->sum('amount'),
                    'count' => $matched->count(),
                ];
            })
            ->sortByDesc(fn (array $row): float => $row['spent'])
            ->values();

        $previous = [];
        for ($i = 1; $i <= 3; $i++) {
            $ym = $this->previousYearMonth($yearMonth, $i);
            $previousRows = (clone $base)
                ->inMonth($ym)
                ->get();

            $previous[] = [
                'year_month' => $ym,
                'label' => Date::createFromFormat('Y-m', $ym)->translatedFormat('M Y'),
                'income' => (float) $previousRows->where('direction', TransactionDirection::Inflow)->sum('amount'),
                'spent' => (float) $previousRows->where('direction', TransactionDirection::Outflow)->sum('amount'),
            ];
        }

        return [
            'year_month' => $yearMonth,
            'month_label' => Date::createFromFormat('Y-m', $yearMonth)->translatedFormat('F Y'),
            'income' => $income,
            'spent' => $spent,
            'net' => $income - $spent,
            'transaction_count' => $rows->count(),
            'categories' => $categories,
            'previous_months' => $previous,
        ];
    }

    private function previousYearMonth(string $yearMonth, int $monthsBack): string
    {
        $carbon = Date::createFromFormat('Y-m', $yearMonth);

        if (! $carbon instanceof Carbon) {
            return $yearMonth;
        }

        return $carbon->subMonths($monthsBack)->format('Y-m');
    }
}
