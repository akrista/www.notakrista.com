<x-filament-panels::page>
    <div class="flex flex-col gap-6">
        {{-- Month switcher and KPI cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1 fi-wi-widget fi-card flex flex-col gap-2 rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('app.budget_current_month') }}
                </span>
                <div class="flex items-center justify-between gap-2">
                    <x-filament::icon-button
                        icon="heroicon-o-chevron-left"
                        color="gray"
                        size="sm"
                        :wire:click="'gotoPreviousMonth()'"
                        :aria-label="__('app.budget_previous_month')"
                    />
                    <span class="font-mono text-base font-semibold text-gray-950 dark:text-white">
                        {{ $this->monthLabel }}
                    </span>
                    <x-filament::icon-button
                        icon="heroicon-o-chevron-right"
                        color="gray"
                        size="sm"
                        :wire:click="'gotoNextMonth()'"
                        :aria-label="__('app.budget_next_month')"
                    />
                </div>
                <x-filament::link
                    size="xs"
                    color="primary"
                    tag="button"
                    :wire:click="'gotoCurrentMonth()'"
                >
                    {{ __('app.budget_current_month') }}
                </x-filament::link>
            </div>

            <div class="md:col-span-1 fi-wi-widget fi-card flex flex-col gap-2 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('app.budget_income') }}
                </span>
                <span class="font-mono text-3xl font-bold text-success-600 dark:text-success-400">
                    {{ $this->incomeLabel }}
                </span>
            </div>

            <div class="md:col-span-1 fi-wi-widget fi-card flex flex-col gap-2 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('app.budget_spent') }}
                </span>
                <span @class([
                    'font-mono text-3xl font-bold',
                    'text-danger-600 dark:text-danger-400' => (float) $this->summary['spent'] > 0,
                    'text-gray-950 dark:text-white' => (float) $this->summary['spent'] === 0.0,
                ])>
                    {{ $this->spentLabel }}
                </span>
            </div>
        </div>

        {{-- Net + due schedules alert --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="fi-wi-widget fi-card flex flex-col gap-2 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('app.budget_to_budget') }}
                </span>
                <span @class([
                    'font-mono text-3xl font-bold',
                    'text-success-600 dark:text-success-400' => (float) $this->summary['net'] > 0,
                    'text-danger-600 dark:text-danger-400' => (float) $this->summary['net'] < 0,
                    'text-gray-950 dark:text-white' => (float) $this->summary['net'] === 0.0,
                ])>
                    {{ $this->netLabel }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('app.budget_page_subheading') }}
                </span>
            </div>
            <div class="fi-wi-widget fi-card flex flex-col gap-2 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('app.budget_due_schedules') }}
                </span>
                <span @class([
                    'font-mono text-3xl font-bold',
                    'text-danger-600 dark:text-danger-400' => $this->dueScheduleCount > 0,
                    'text-gray-950 dark:text-white' => $this->dueScheduleCount === 0,
                ])>
                    {{ $this->dueScheduleCount }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('app.budget_due_schedules_helper') }}
                </span>
            </div>
        </div>

        {{-- Category breakdown --}}
        <x-filament::section
            :heading="__('app.budget_by_category')"
            :description="__('app.budget_by_category_description')"
            collapsible
        >
            @if ($this->summary['categories']->isEmpty())
                <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                    {{ __('app.no_data') }}
                </div>
            @else
                <div class="flex flex-col divide-y divide-gray-100 dark:divide-white/10">
                    @foreach ($this->summary['categories'] as $cat)
                        <div class="grid grid-cols-12 items-center gap-3 py-2">
                            <div class="col-span-6 flex items-center gap-2">
                                <span class="text-base">{{ $cat['icon'] ?? '🏷️' }}</span>
                                <span class="font-medium text-gray-950 dark:text-white">
                                    {{ $cat['name'] }}
                                </span>
                            </div>
                            <div class="col-span-3 text-right font-mono text-xs text-gray-500 dark:text-gray-400">
                                {{ $cat['count'] }} {{ __('app.budget_transactions_short') }}
                            </div>
                            <div class="col-span-3 text-right font-mono text-sm font-semibold text-gray-950 dark:text-white">
                                ${{ number_format((float) $cat['spent'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>

        {{-- Last 3 months strip --}}
        <x-filament::section :heading="__('app.budget_last_3_months')" collapsible>
            <div class="flex flex-col gap-1 font-mono text-xs">
                @foreach ($this->summary['previous_months'] as $prev)
                    <div class="grid grid-cols-12 gap-2">
                        <span class="col-span-3 text-gray-500 dark:text-gray-400">{{ $prev['label'] }}</span>
                        <span class="col-span-3 text-gray-950 dark:text-white">
                            {{ __('app.budget_income') }}:
                            ${{ number_format((float) $prev['income'], 2) }}
                        </span>
                        <span class="col-span-3 text-gray-950 dark:text-white">
                            {{ __('app.budget_spent') }}:
                            ${{ number_format((float) $prev['spent'], 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
