<?php

declare(strict_types=1);

namespace App\Filament\Resources\Schedules;

use App\Actions\Schedules\PostScheduleAction;
use App\Enums\BillCadence;
use App\Enums\TransactionDirection;
use App\Filament\Resources\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Schedules\Pages\ListSchedules;
use App\Models\Account;
use App\Models\Schedule;
use App\Models\TransactionCategory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Override;

final class ScheduleResource extends Resource
{
    #[Override]
    protected static ?string $model = Schedule::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClock;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    protected static ?int $navigationSort = 803;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.schedule');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.schedules');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.budget');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'payee_name'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('account_id')
                            ->label(__('fields.account_id'))
                            ->relationship('account', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Account $record): string => $record->name . ' (' . $record->currency . ')')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload(),
                        Select::make('category_id')
                            ->label(__('fields.category_id'))
                            ->relationship('category', 'name')
                            ->getOptionLabelFromRecordUsing(fn (TransactionCategory $record): string => ($record->icon ?? '🏷️') . ' ' . $record->name)
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder(__('app.none')),
                        TextInput::make('payee_name')
                            ->label(__('fields.payee_name'))
                            ->maxLength(255)
                            ->placeholder('Netflix, IMAS, …'),
                        Select::make('direction')
                            ->label(__('fields.direction'))
                            ->options(TransactionDirection::class)
                            ->required()
                            ->native(false)
                            ->default(TransactionDirection::Outflow->value),
                        TextInput::make('amount')
                            ->label(__('fields.amount'))
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->prefix('$'),
                        Select::make('cadence')
                            ->label(__('fields.cadence'))
                            ->options(BillCadence::class)
                            ->required()
                            ->native(false)
                            ->default(BillCadence::Monthly->value),
                        DatePicker::make('next_run_on')
                            ->label(__('fields.next_run_on'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        DatePicker::make('last_run_on')
                            ->label(__('fields.last_run_on'))
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(),
                        Textarea::make('memo')
                            ->label(__('fields.memo'))
                            ->rows(2)
                            ->columnSpanFull(),
                        Toggle::make('auto_post')
                            ->label(__('app.schedule_auto_post'))
                            ->helperText(__('app.schedule_auto_post_helper'))
                            ->default(true)
                            ->inline(false),
                        Toggle::make('is_public')
                            ->label(__('fields.is_public'))
                            ->helperText(__('app.helper_schedule_is_public'))
                            ->default(false)
                            ->inline(false),
                        Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['account', 'category']))
            ->defaultSort('next_run_on', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('account.name')
                    ->label(__('fields.account_id'))
                    ->badge()
                    ->color(fn (?Account $account): ?string => $account?->color_token),
                TextColumn::make('direction')
                    ->label(__('fields.direction'))
                    ->badge()
                    ->color(fn (TransactionDirection $state): string => $state->colorToken())
                    ->formatStateUsing(fn (TransactionDirection $state): string => $state->label()),
                TextColumn::make('category.name')
                    ->label(__('fields.category_id'))
                    ->getStateUsing(fn (Schedule $record): string => $record->category ? (($record->category->icon ?? '🏷️') . ' ' . $record->category->name) : '—')
                    ->placeholder('—'),
                TextColumn::make('cadence')
                    ->label(__('fields.cadence'))
                    ->badge()
                    ->color(fn (BillCadence $state): string => $state->colorToken())
                    ->formatStateUsing(fn (BillCadence $state): string => $state->label()),
                TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('USD')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('next_run_on')
                    ->label(__('fields.next_run_on'))
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Schedule $record): ?string => $record->isDue() ? 'red' : null),
                TextColumn::make('last_run_on')
                    ->label(__('fields.last_run_on'))
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(),
                IconColumn::make('auto_post')
                    ->label(__('app.schedule_auto_post'))
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
                IconColumn::make('is_public')
                    ->label(__('fields.is_public'))
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('cadence')
                    ->options(BillCadence::class),
                SelectFilter::make('account_id')
                    ->label(__('fields.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                self::postNowAction(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
        ];
    }

    private static function postNowAction(): Action
    {
        return Action::make('postNow')
            ->label(__('app.schedule_post_now'))
            ->icon('heroicon-o-play')
            ->color('primary')
            ->modalHeading(__('app.schedule_post_now_heading'))
            ->modalDescription(__('app.schedule_post_now_description'))
            ->requiresConfirmation()
            ->action(function (Schedule $record): void {
                $transaction = resolve(PostScheduleAction::class)->handle($record, Date::now());

                if ($transaction === null) {
                    Notification::make()
                        ->title(__('app.no_data'))
                        ->info()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('app.schedule_posted', [
                        'name' => (string) $record->name,
                    ]))
                    ->success()
                    ->send();
            });
    }
}
