<?php

declare(strict_types=1);

namespace App\Filament\Resources\Accounts;

use App\Enums\AccountType;
use App\Filament\Resources\Accounts\Pages\ListAccounts;
use App\Models\Account;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class AccountResource extends Resource
{
    #[Override]
    protected static ?string $model = Account::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedWallet;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    protected static ?int $navigationSort = 600;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.account');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.accounts');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.finance');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'type', 'currency'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Account $record */
        return [
            __('fields.type') => $record->type->label(),
            __('fields.currency') => $record->currency,
        ];
    }

    /**
     * Account `currency` is a display label for the bank/exchange asset
     * (Bancamiga is VES, PayPal is USD, USDT is USDT). It is not used in
     * any transactional math: all Transaction amounts are USD.
     *
     * @return array<string, string>
     */
    public static function getCurrencyOptions(): array
    {
        return [
            'USD' => 'USD',
            'VES' => 'VES',
            'USDT' => 'USDT',
            'EUR' => 'EUR',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('fields.section_account_details'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('type')
                            ->label(__('fields.type'))
                            ->options(AccountType::class)
                            ->required()
                            ->native(false),
                        Select::make('currency')
                            ->label(__('fields.currency'))
                            ->options(self::getCurrencyOptions())
                            ->default('USD')
                            ->required()
                            ->native(false)
                            ->helperText(__('app.helper_currency')),
                        TextInput::make('opening_balance')
                            ->label(__('fields.opening_balance'))
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->step(0.01),
                        TextInput::make('icon')
                            ->label(__('fields.icon'))
                            ->helperText(__('fields.helper_icon_emoji'))
                            ->maxLength(16),
                        TextInput::make('color_token')
                            ->label(__('fields.color_token'))
                            ->helperText(__('fields.helper_color_token'))
                            ->maxLength(32)
                            ->placeholder('muted'),
                        TextInput::make('position')
                            ->label(__('fields.position'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('fields.helper_position')),
                        Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true)
                            ->inline(false),
                        Textarea::make('notes')
                            ->label(__('fields.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('fields.section_donation_info'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('donation_url')
                            ->label(__('fields.donation_url'))
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('donation_account_number')
                            ->label(__('fields.donation_account_number'))
                            ->maxLength(255),
                        TextInput::make('donation_aba')
                            ->label(__('fields.donation_aba'))
                            ->maxLength(255),
                        TextInput::make('donation_swift')
                            ->label(__('fields.donation_swift'))
                            ->maxLength(255),
                        TextInput::make('donation_id_cedula')
                            ->label(__('fields.donation_id_cedula'))
                            ->maxLength(255),
                        TextInput::make('donation_qr_image')
                            ->label(__('fields.donation_qr_image'))
                            ->maxLength(255)
                            ->helperText(__('app.helper_donation_qr')),
                        Textarea::make('donation_address')
                            ->label(__('fields.donation_address'))
                            ->rows(3)
                            ->helperText(__('app.helper_donation_address'))
                            ->columnSpanFull(),
                        Textarea::make('donation_instructions')
                            ->label(__('fields.donation_instructions'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('')
                    ->alignCenter()
                    ->width('60px'),
                TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label(__('fields.type'))
                    ->badge()
                    ->color(fn (AccountType $state): string => $state->colorToken())
                    ->formatStateUsing(fn (AccountType $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(__('fields.currency'))
                    ->badge()
                    ->color('muted')
                    ->alignCenter(),
                TextColumn::make('opening_balance')
                    ->label(__('fields.opening_balance'))
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean()
                    ->alignCenter(),
                IconColumn::make('has_donation_info')
                    ->label(__('fields.donation'))
                    ->boolean()
                    ->state(fn (Account $record): bool => $record->hasDonationInfo())
                    ->alignCenter()
                    ->toggleable(),
                TextColumn::make('position')
                    ->label(__('fields.position'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(AccountType::class),
                TrashedFilter::make(),
            ])
            ->defaultSort('position')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAccounts::route('/'),
        ];
    }
}
