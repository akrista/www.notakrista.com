<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Schedule;
use App\Services\Budget\MonthlySummaryService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Url;
use Override;

/**
 * @property-read Collection<int, array<string, mixed>> $summary
 * @property-read string $incomeLabel
 * @property-read string $spentLabel
 * @property-read string $netLabel
 * @property-read string $monthLabel
 * @property-read int $dueScheduleCount
 */
final class BudgetPage extends Page
{
    #[Url(as: 'month')]
    public string $yearMonth = '';

    #[Override]
    protected string $view = 'filament.pages.budget-page';

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedWallet;

    #[Override]
    protected static ?string $title = 'Budget Dashboard';

    #[Override]
    protected static ?int $navigationSort = 600;

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.budget');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.budget_page_title');
    }

    public function mount(): void
    {
        $this->yearMonth = $this->yearMonth !== '' ? $this->yearMonth : now()->format('Y-m');
    }

    public function getHeading(): string
    {
        return __('app.budget_page_title');
    }

    public function getSubheading(): string
    {
        return __('app.budget_page_subheading');
    }

    public function gotoPreviousMonth(): void
    {
        $this->yearMonth = $this->shiftMonth($this->yearMonth, -1);
    }

    public function gotoNextMonth(): void
    {
        $this->yearMonth = $this->shiftMonth($this->yearMonth, 1);
    }

    public function gotoCurrentMonth(): void
    {
        $this->yearMonth = now()->format('Y-m');
    }

    /**
     * @return array<string, mixed>
     */
    public function getSummaryProperty(): array
    {
        return resolve(MonthlySummaryService::class)
            ->for($this->yearMonth, publicOnly: false);
    }

    public function getIncomeLabelProperty(): string
    {
        $summary = $this->getSummaryProperty();

        return number_format((float) $summary['income'], 2) . ' USD';
    }

    public function getSpentLabelProperty(): string
    {
        $summary = $this->getSummaryProperty();

        return number_format((float) $summary['spent'], 2) . ' USD';
    }

    public function getNetLabelProperty(): string
    {
        $summary = $this->getSummaryProperty();

        return number_format((float) $summary['net'], 2) . ' USD';
    }

    public function getMonthLabelProperty(): string
    {
        $carbon = Date::createFromFormat('Y-m', $this->yearMonth);

        if (! $carbon instanceof Carbon) {
            return $this->yearMonth;
        }

        return $carbon->translatedFormat('F Y');
    }

    public function getDueScheduleCountProperty(): int
    {
        return Schedule::query()
            ->active()
            ->where('next_run_on', '<=', now()->toDateString())
            ->count();
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    private function shiftMonth(string $yearMonth, int $delta): string
    {
        $carbon = Date::createFromFormat('Y-m', $yearMonth);

        if (! $carbon instanceof Carbon) {
            return $yearMonth;
        }

        return $carbon->addMonths($delta)->format('Y-m');
    }
}
