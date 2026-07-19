<?php

declare(strict_types=1);

namespace App\Filament\Resources\MtgCards;

use App\Filament\Resources\MtgCards\Pages\ListMtgCards;
use App\Jobs\SyncMtgCardsJob;
use App\Models\MtgCard;
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
use Illuminate\Support\HtmlString;
use Override;

final class MtgCardResource extends Resource
{
    #[Override]
    protected static ?string $model = MtgCard::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCreditCard;

    #[Override]
    protected static ?string $recordTitleAttribute = 'set';

    #[Override]
    protected static ?int $navigationSort = 800;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.mtg_card');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.mtg_cards');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.inventory');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['set', 'number'];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var MtgCard $record */
        return [
            __('fields.mtg_card_number') => $record->number,
            __('fields.mtg_card_quantity') => (string) $record->quantity,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('summary')
                    ->hiddenLabel()
                    ->content(
                        fn (?MtgCard $record): ?string => $record instanceof MtgCard
                        ? sprintf(
                            '%s | %s | %s | %s USD',
                            $record->name ?? 'Not synced',
                            $record->type_line ?? '-',
                            ucfirst($record->rarity ?? '-'),
                            $record->price ?? '0.00'
                        )
                        : null
                    )
                    ->hiddenOn('create')
                    ->columnSpanFull(),

                Section::make('Collection Details')
                    ->columns(4)
                    ->schema([
                        TextInput::make('set')
                            ->label(__('fields.mtg_card_set'))
                            ->required()
                            ->maxLength(10)
                            ->placeholder('e.g. aer, akh')
                            ->extraInputAttributes(['style' => 'text-transform: lowercase;']),
                        TextInput::make('number')
                            ->label(__('fields.mtg_card_number'))
                            ->required()
                            ->maxLength(10)
                            ->placeholder('e.g. 52, 278'),
                        TextInput::make('quantity')
                            ->label(__('fields.mtg_card_quantity'))
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
                            ->label(__('fields.mtg_card_name'))
                            ->live(onBlur: true)
                            ->maxLength(255),
                        TextInput::make('type_line')
                            ->label(__('fields.mtg_card_type'))
                            ->live(onBlur: true)
                            ->maxLength(255),
                        TextInput::make('mana_cost')
                            ->label(__('fields.mtg_card_mana_cost'))
                            ->live(onBlur: true)
                            ->maxLength(255),
                        TextInput::make('rarity')
                            ->label(__('fields.mtg_card_rarity'))
                            ->live(onBlur: true)
                            ->maxLength(50),
                        TextInput::make('price')
                            ->label(__('fields.mtg_card_price'))
                            ->numeric()
                            ->live(onBlur: true)
                            ->prefix('$'),
                        TextInput::make('image_url')
                            ->label(__('fields.mtg_card_image'))
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
                                                <div class="text-sm font-semibold">%s</div>
                                                <div class="text-sm text-primary-500 font-bold">$%s USD</div>
                                            </div>
                                        </div>',
                                        $get('image_url')
                                            ? sprintf('<img src="%s" alt="%s" class="w-40 rounded-lg shadow-md" />', e($get('image_url')), e($get('name') ?? 'Card Image'))
                                            : '<div class="w-40 h-56 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-xs shadow-md border border-dashed border-gray-300">No Image</div>',
                                        e($get('name') ?? 'Unknown Card'),
                                        e($get('type_line') ?? '-'),
                                        self::formatManaCost($get('mana_cost'))?->toHtml() ?? '',
                                        $get('price') ?? '0.00'
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
                    ->label(__('fields.mtg_card_image'))
                    ->width(40)
                    ->height(56),
                TextColumn::make('name')
                    ->label(__('fields.mtg_card_name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not synced')
                    ->description(fn (MtgCard $record): string => mb_strtoupper($record->set) . ' #' . $record->number),
                TextColumn::make('mana_cost')
                    ->label(__('fields.mtg_card_mana_cost'))
                    ->html()
                    ->alignCenter()
                    ->state(fn (MtgCard $record): ?HtmlString => self::formatManaCost($record->mana_cost))
                    ->placeholder('-'),
                TextColumn::make('type_line')
                    ->label(__('fields.mtg_card_type'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('rarity')
                    ->label(__('fields.mtg_card_rarity'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'common' => 'gray',
                        'uncommon' => 'info',
                        'rare' => 'warning',
                        'mythic' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('fields.mtg_card_price'))
                    ->money('USD')
                    ->sortable()
                    ->alignRight()
                    ->placeholder('-'),
                TextColumn::make('quantity')
                    ->label(__('fields.mtg_card_quantity'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_sold')
                    ->label(__('fields.is_sold'))
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('total_value')
                    ->label(__('fields.mtg_card_total_value'))
                    ->state(fn (MtgCard $record): ?float => $record->price !== null ? ($record->price * $record->quantity) : null)
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
                    ->label('Sync with Scryfall')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (): void {
                        dispatch(new SyncMtgCardsJob());
                    })
                    ->requiresConfirmation()
                    ->successNotificationTitle('MTG Card Sync Queued'),
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
            'index' => ListMtgCards::route('/'),
        ];
    }

    public static function formatManaCost(?string $manaCost): ?HtmlString
    {
        if (in_array($manaCost, [null, '', '0'], true)) {
            return null;
        }

        preg_match_all('/\{([^}]+)\}/', $manaCost, $matches);

        if (empty($matches[1])) {
            return new HtmlString(e($manaCost));
        }

        $html = '<div class="flex items-center gap-1 inline-flex">';
        foreach ($matches[1] as $symbol) {
            $symbol = mb_strtoupper($symbol);

            $style = 'width: 1.25rem; height: 1.25rem; font-size: 0.75rem; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-family: monospace; border: 1px solid rgba(0,0,0,0.15); line-height: 1; text-transform: uppercase;';

            match ($symbol) {
                'W' => $style .= ' background-color: #fffcd5; color: #4a3c10;',
                'U' => $style .= ' background-color: #c1d7e9; color: #0d2c54;',
                'B' => $style .= ' background-color: #2b252c; color: #fbfbfb;',
                'R' => $style .= ' background-color: #f8cbd4; color: #5c0f18;',
                'G' => $style .= ' background-color: #cae8d5; color: #10381f;',
                default => $style .= ' background-color: #d3d3d3; color: #333333;',
            };

            $html .= sprintf('<span style="%s" title="%s">%s</span>', $style, e($symbol), e($symbol));
        }

        $html .= '</div>';

        return new HtmlString($html);
    }
}
