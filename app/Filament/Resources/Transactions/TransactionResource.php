<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions;

use App\Actions\Transactions\MarkTransactionPostedAction;
use App\Enums\TransactionDirection;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Models\Account;
use App\Models\Transaction;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Override;

final class TransactionResource extends Resource
{
    #[Override]
    protected static ?string $model = Transaction::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    #[Override]
    protected static ?string $recordTitleAttribute = 'memo';

    #[Override]
    protected static ?int $navigationSort = 801;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.transactions');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.budget');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['memo', 'payee_name', 'account.name'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
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
                        DatePicker::make('occurred_on')
                            ->label(__('fields.occurred_on'))
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        DatePicker::make('posted_at')
                            ->label(__('fields.posted_at'))
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder(__('app.unposted'))
                            ->helperText(__('app.helper_posted_at')),
                        Toggle::make('is_public')
                            ->label(__('fields.is_public'))
                            ->helperText(__('app.helper_is_public'))
                            ->default(false)
                            ->inline(false),
                        Textarea::make('memo')
                            ->label(__('fields.memo'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['account', 'category']))
            ->defaultSort('occurred_on', 'desc')
            ->columns([
                TextColumn::make('occurred_on')
                    ->label(__('fields.occurred_on'))
                    ->date('d/m/Y')
                    ->sortable(),
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
                    ->getStateUsing(fn (Transaction $record): string => $record->category ? (($record->category->icon ?? '🏷️') . ' ' . $record->category->name) : '—')
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('USD')
                    ->alignEnd()
                    ->sortable(),
                IconColumn::make('is_posted')
                    ->label(__('fields.posted_at'))
                    ->boolean()
                    ->state(fn (Transaction $record): bool => $record->isPosted())
                    ->alignCenter(),
                IconColumn::make('is_public')
                    ->label(__('fields.is_public'))
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('payee_name')
                    ->label(__('fields.payee_name'))
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('memo')
                    ->label(__('fields.memo'))
                    ->placeholder('—')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->label(__('fields.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category_id')
                    ->label(__('fields.category_id'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('direction')
                    ->label(__('fields.direction'))
                    ->options(TransactionDirection::class),
                TernaryFilter::make('is_public')
                    ->label(__('fields.is_public')),
                Filter::make('posted')
                    ->label(__('fields.posted_at'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('posted_at')),
                Filter::make('unposted')
                    ->label(__('app.unposted'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNull('posted_at')),
                Filter::make('month')
                    ->label(__('fields.occurred_on'))
                    ->form([
                        TextInput::make('year_month')
                            ->label('YYYY-MM')
                            ->placeholder('2026-08'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $month = $data['year_month'] ?? null;

                        return $query->when(
                            filled($month) && is_string($month),
                            fn (Builder $q): Builder => $q->inMonth($month),
                        );
                    }),
            ])
            ->recordActions([
                self::markPostedAction(),
                self::markUnpostedAction(),
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
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }

    private static function markPostedAction(): Action
    {
        return Action::make('markPosted')
            ->label(__('app.transaction_mark_posted'))
            ->icon('heroicon-o-check-circle')
            ->color('accent')
            ->visible(fn (Transaction $record): bool => ! $record->isPosted())
            ->requiresConfirmation()
            ->action(function (Transaction $record): void {
                $ok = resolve(MarkTransactionPostedAction::class)->handle($record, Date::now());
                if ($ok) {
                    Notification::make()
                        ->title(__('app.transaction_marked_posted'))
                        ->success()
                        ->send();
                }
            });
    }

    private static function markUnpostedAction(): Action
    {
        return Action::make('markUnposted')
            ->label(__('app.transaction_mark_unposted'))
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('muted')
            ->visible(fn (Transaction $record): bool => $record->isPosted())
            ->requiresConfirmation()
            ->action(function (Transaction $record): void {
                $record->markUnposted();
                Notification::make()
                    ->title(__('app.transaction_marked_unposted'))
                    ->info()
                    ->send();
            });
    }
}
