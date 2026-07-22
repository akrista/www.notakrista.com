<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarframeAccounts;

use App\Filament\Resources\WarframeAccounts\Pages\EditWarframeAccount;
use App\Filament\Resources\WarframeAccounts\Pages\ListWarframeAccounts;
use App\Models\WarframeAccount;
use App\Models\WarframeUserItem;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;

final class WarframeAccountResource extends Resource
{
    #[Override]
    protected static ?string $model = WarframeAccount::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;

    #[Override]
    protected static ?string $recordTitleAttribute = 'account_name';

    #[Override]
    protected static ?int $navigationSort = 850;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return 'Warframe Profile';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Warframe Profile';
    }

    public static function getNavigationGroup(): string
    {
        return 'Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('WarframeProfileTabs')
                    ->tabs([
                        Tab::make('Profile Summary & Wallet')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Placeholder::make('summary')
                                    ->hiddenLabel()
                                    ->content(
                                        fn (?WarframeAccount $record): ?string => $record instanceof WarframeAccount
                                        ? sprintf(
                                            'Mastery Rank %d | 🪙 Credits: %s | 💎 Platinum: %s | ⚡ Void Traces: %s | ✨ Endo: %s',
                                            $record->mastery_rank,
                                            number_format($record->credits),
                                            number_format($record->platinum),
                                            number_format($record->void_traces),
                                            number_format($record->endo)
                                        )
                                        : null
                                    )
                                    ->hiddenOn('create'),

                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('account_name')
                                            ->label('Account Name')
                                            ->required(),

                                        TextInput::make('mastery_rank')
                                            ->label('Mastery Rank')
                                            ->numeric()
                                            ->default(0),

                                        TextInput::make('credits')
                                            ->label('Credits 🪙')
                                            ->numeric()
                                            ->disabled(),

                                        TextInput::make('platinum')
                                            ->label('Platinum 💎')
                                            ->numeric()
                                            ->disabled(),

                                        TextInput::make('void_traces')
                                            ->label('Void Traces ⚡')
                                            ->numeric()
                                            ->disabled(),

                                        TextInput::make('endo')
                                            ->label('Endo ✨')
                                            ->numeric()
                                            ->disabled(),

                                        TextInput::make('active_avatar')
                                            ->label('Active Avatar Type')
                                            ->columnSpan(2),

                                        DateTimePicker::make('last_imported_at')
                                            ->label('Last Imported At')
                                            ->disabled()
                                            ->columnSpan(2),
                                    ]),
                            ]),

                        Tab::make('Warframes & Vehicles')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Repeater::make('warframe_list')
                                    ->label('Owned Warframes, Archwings & Nechramechs')
                                    ->relationship('userItems', modifyQueryUsing: fn ($query) => $query->whereIn('category', ['Warframe', 'Archwing', 'Nechramech'])->with('catalogItem')->take(100))
                                    ->schema([
                                        TextInput::make('item_name')
                                            ->label('Item')
                                            ->formatStateUsing(fn ($state, WarframeUserItem $record): string => sprintf('%s (%s)', $record->catalogItem?->name ?? basename($record->item_type), $record->category))
                                            ->disabled(),

                                        TextInput::make('level')
                                            ->label('Rank')
                                            ->numeric()
                                            ->disabled(),

                                        TextInput::make('formas')
                                            ->label('Formas')
                                            ->numeric()
                                            ->disabled(),
                                    ])
                                    ->columns(3)
                                    ->itemLabel(fn (array $state): ?string => isset($state['item_type']) ? sprintf('%s [%s]', basename($state['item_type']), $state['category'] ?? 'Warframe') : null)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Weapons')
                            ->icon('heroicon-o-fire')
                            ->schema([
                                Repeater::make('weapon_list')
                                    ->label('Owned Primary, Secondary, Melee & Arch-Weapons')
                                    ->relationship('userItems', modifyQueryUsing: fn ($query) => $query->whereIn('category', ['Primary', 'Secondary', 'Melee', 'ArchWeapon'])->with('catalogItem')->take(100))
                                    ->schema([
                                        TextInput::make('item_name')
                                            ->label('Weapon')
                                            ->formatStateUsing(fn ($state, WarframeUserItem $record): string => sprintf('%s (%s)', $record->catalogItem?->name ?? basename($record->item_type), $record->category))
                                            ->disabled(),

                                        TextInput::make('level')
                                            ->label('Rank')
                                            ->numeric()
                                            ->disabled(),

                                        TextInput::make('formas')
                                            ->label('Formas')
                                            ->numeric()
                                            ->disabled(),
                                    ])
                                    ->columns(3)
                                    ->itemLabel(fn (array $state): ?string => isset($state['item_type']) ? sprintf('%s [%s]', basename($state['item_type']), $state['category'] ?? 'Weapon') : null)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Mods & Rivens')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Repeater::make('mod_list')
                                    ->label('Owned Mods & Riven Mods (Top 100)')
                                    ->relationship('userItems', modifyQueryUsing: fn ($query) => $query->whereIn('category', ['Mod', 'Riven'])->with('catalogItem')->take(100))
                                    ->schema([
                                        TextInput::make('item_name')
                                            ->label('Mod Name')
                                            ->formatStateUsing(fn ($state, WarframeUserItem $record): string => sprintf('%s [%s]', $record->catalogItem?->name ?? basename($record->item_type), $record->category))
                                            ->disabled(),

                                        TextInput::make('fusion_rank')
                                            ->label('Fusion Rank')
                                            ->formatStateUsing(fn ($state, WarframeUserItem $record): string => sprintf('Rank %d/%d', $record->fusion_rank ?? 0, $record->max_fusion_rank ?? 10))
                                            ->disabled(),

                                        TextInput::make('item_count')
                                            ->label('Count')
                                            ->numeric()
                                            ->disabled(),
                                    ])
                                    ->columns(3)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Void Relics')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Repeater::make('relic_list')
                                    ->label('Owned Void Relics (Top 100)')
                                    ->relationship('userItems', modifyQueryUsing: fn ($query) => $query->where('category', 'Relic')->with('catalogItem')->take(100))
                                    ->schema([
                                        TextInput::make('item_name')
                                            ->label('Relic')
                                            ->formatStateUsing(fn ($state, WarframeUserItem $record): string => sprintf('%s (%s)', $record->catalogItem?->name ?? basename($record->item_type), $record->refinement ?? 'Intact'))
                                            ->disabled(),

                                        TextInput::make('refinement')
                                            ->label('Refinement')
                                            ->disabled(),

                                        TextInput::make('item_count')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->disabled(),
                                    ])
                                    ->columns(3)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Active Boosters')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                Textarea::make('boosters')
                                    ->label('Boosters JSON')
                                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                                    ->rows(8)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_name')
                    ->label('Profile Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mastery_rank')
                    ->label('Mastery Rank')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('credits')
                    ->label('Credits 🪙')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('platinum')
                    ->label('Platinum 💎')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_warframes')
                    ->label('Warframes')
                    ->badge()
                    ->color('success'),

                TextColumn::make('total_weapons')
                    ->label('Weapons')
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_relics')
                    ->label('Relics')
                    ->badge()
                    ->color('secondary'),

                TextColumn::make('last_imported_at')
                    ->label('Last Sync')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListWarframeAccounts::route('/'),
            'edit' => EditWarframeAccount::route('/{record}/edit'),
        ];
    }
}
