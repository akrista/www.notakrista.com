<?php

declare(strict_types=1);

namespace App\Filament\Resources\YugiohCards;

use App\Filament\Resources\YugiohCards\Pages\ListYugiohCards;
use App\Jobs\SyncYugiohCardsJob;
use App\Models\YugiohCard;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Override;

final class YugiohCardResource extends Resource
{
    #[Override]
    protected static ?string $model = YugiohCard::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCreditCard;

    #[Override]
    protected static ?string $recordTitleAttribute = 'setcode';

    #[Override]
    protected static ?int $navigationSort = 810;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.yugioh_card');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.yugioh_cards');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.inventory');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['setcode', 'name'];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var YugiohCard $record */
        return [
            __('fields.yugioh_card_name') => $record->name ?? '-',
            __('fields.yugioh_card_quantity') => (string) $record->quantity,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('summary')
                    ->hiddenLabel()
                    ->content(
                        fn (?YugiohCard $record): ?string => $record instanceof YugiohCard
                        ? sprintf(
                            '%s | %s | %s | Set: %s USD | Card: %s USD',
                            $record->name ?? 'Not synced',
                            $record->type ?? '-',
                            ucfirst($record->rarity ?? '-'),
                            $record->price ?? '0.00',
                            $record->card_price ?? '0.00'
                        )
                        : null
                    )
                    ->hiddenOn('create')
                    ->columnSpanFull(),

                Section::make('Collection Details')
                    ->columns(3)
                    ->schema([
                        TextInput::make('setcode')
                            ->label(__('fields.yugioh_card_setcode'))
                            ->required()
                            ->maxLength(20)
                            ->placeholder('e.g. 5DS1-EN021')
                            ->extraInputAttributes(['style' => 'text-transform: uppercase;']),
                        TextInput::make('quantity')
                            ->label(__('fields.yugioh_card_quantity'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(1),
                        Toggle::make('is_sold')
                            ->label(__('fields.is_sold'))
                            ->inline(false)
                            ->default(false),
                    ])
                    ->columnSpanFull(),

                Section::make('Manual Card Details (Overrides)')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.yugioh_card_name'))
                            ->live(onBlur: true)
                            ->maxLength(255),
                        TextInput::make('type')
                            ->label(__('fields.yugioh_card_type'))
                            ->live(onBlur: true)
                            ->maxLength(255),
                        TextInput::make('rarity')
                            ->label(__('fields.yugioh_card_rarity'))
                            ->live(onBlur: true)
                            ->maxLength(50),
                        TextInput::make('price')
                            ->label(__('fields.yugioh_card_price'))
                            ->numeric()
                            ->live(onBlur: true)
                            ->prefix('$'),
                        TextInput::make('card_price')
                            ->label(__('fields.yugioh_card_card_price'))
                            ->numeric()
                            ->live(onBlur: true)
                            ->prefix('$'),
                        TextInput::make('image_url')
                            ->label(__('fields.yugioh_card_image'))
                            ->live(onBlur: true)
                            ->maxLength(2048),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Card Details')
                    ->schema([
                        Placeholder::make('card_preview')
                            ->hiddenLabel()
                            ->content(
                                fn (Get $get): HtmlString => new HtmlString(
                                    sprintf(
                                        '<div class="flex flex-col items-center gap-4 text-center">
                                            %s
                                            <div class="space-y-1">
                                                <div class="font-bold text-lg">%s</div>
                                                <div class="text-xs text-gray-500">%s</div>
                                                <div class="text-sm text-gray-400">Set Price: $%s USD</div>
                                                <div class="text-sm text-primary-500 font-bold">Card Price: $%s USD</div>
                                            </div>
                                        </div>',
                                        $get('image_url')
                                            ? sprintf(
                                                '<img src="%s" alt="%s" class="w-40 rounded-lg shadow-md" />',
                                                e(
                                                    (str_starts_with((string) $get('image_url'), 'http://') || str_starts_with((string) $get('image_url'), 'https://'))
                                                        ? $get('image_url')
                                                        : Storage::disk('public')->url($get('image_url'))
                                                ),
                                                e($get('name') ?? 'Card Image')
                                            )
                                            : '<div class="w-40 h-56 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-xs shadow-md border border-dashed border-gray-300">No Image</div>',
                                        e($get('name') ?? 'Unknown Card'),
                                        e($get('type') ?? '-'),
                                        $get('price') ?? '0.00',
                                        $get('card_price') ?? '0.00'
                                    )
                                )
                            ),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label(__('fields.yugioh_card_image'))
                    ->width(40)
                    ->height(56),
                TextColumn::make('name')
                    ->label(__('fields.yugioh_card_name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not synced')
                    ->description(fn (YugiohCard $record): string => $record->setcode),
                TextColumn::make('type')
                    ->label(__('fields.yugioh_card_type'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('rarity')
                    ->label(__('fields.yugioh_card_rarity'))
                    ->badge()
                    ->color(fn (?string $state): string => match (mb_strtolower($state ?? '')) {
                        'common' => 'gray',
                        'rare' => 'info',
                        'super rare' => 'primary',
                        'ultra rare' => 'warning',
                        'secret rare' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('fields.yugioh_card_price'))
                    ->money('USD')
                    ->sortable()
                    ->alignRight()
                    ->placeholder('-'),
                TextColumn::make('card_price')
                    ->label(__('fields.yugioh_card_card_price'))
                    ->money('USD')
                    ->sortable()
                    ->alignRight()
                    ->placeholder('-'),
                TextColumn::make('quantity')
                    ->label(__('fields.yugioh_card_quantity'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_sold')
                    ->label(__('fields.is_sold'))
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('total_value')
                    ->label(__('fields.yugioh_card_total_value'))
                    ->state(fn (YugiohCard $record): ?float => $record->price !== null ? ($record->price * $record->quantity) : null)
                    ->money('USD')
                    ->sortable()
                    ->alignRight()
                    ->placeholder('-'),
                TextColumn::make('total_card_value')
                    ->label(__('fields.yugioh_card_total_card_value'))
                    ->state(fn (YugiohCard $record): ?float => $record->card_price !== null ? ($record->card_price * $record->quantity) : null)
                    ->money('USD')
                    ->sortable()
                    ->alignRight()
                    ->placeholder('-'),
            ])
            ->filters([
                TernaryFilter::make('is_sold')
                    ->label(__('fields.is_sold')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                Action::make('sync_all')
                    ->label('Sync with YGOPRODeck')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (): void {
                        $ids = YugiohCard::query()->pluck('id')->toArray();
                        foreach (array_chunk($ids, 25) as $chunk) {
                            dispatch(new SyncYugiohCardsJob($chunk));
                        }
                    })
                    ->requiresConfirmation()
                    ->successNotificationTitle('Yu-Gi-Oh Card Sync Queued'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListYugiohCards::route('/'),
        ];
    }
}
