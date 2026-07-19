<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items;

use App\Actions\Translations\SyncModelTranslationLinesAction;
use App\Enums\EquipmentSlot;
use App\Enums\ItemLoadout;
use App\Enums\ItemRarity;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Models\Category;
use App\Models\Item;
use App\Models\LanguageLine;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class ItemResource extends Resource
{
    #[Override]
    protected static ?string $model = Item::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArchiveBox;

    #[Override]
    protected static ?string $recordTitleAttribute = 'slug';

    #[Override]
    protected static ?int $navigationSort = 700;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.items');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.inventory');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Item $record */
        return [
            __('fields.rarity') => $record->rarity?->label() ?? '—',
            __('fields.category') => $record->category?->name('en') ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Item')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('sections.display'))
                            ->icon(Heroicon::OutlinedEye)
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('slug')
                                        ->label(__('fields.slug'))
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(Item::class, 'slug', ignoreRecord: true)
                                        ->helperText(__('fields.helper_slug')),
                                    Select::make('rarity')
                                        ->label(__('fields.rarity'))
                                        ->options(ItemRarity::class)
                                        ->required()
                                        ->native(false),
                                    TextInput::make('icon')
                                        ->label(__('fields.icon'))
                                        ->helperText(__('fields.helper_icon_emoji'))
                                        ->maxLength(16),
                                    FileUpload::make('image_url')
                                        ->label(__('fields.image'))
                                        ->disk('public')
                                        ->directory('items')
                                        ->visibility('public')
                                        ->image()
                                        ->columnSpanFull(),
                                ]),
                                Section::make(__('sections.translations'))
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name_en')
                                            ->label(__('fields.name_en'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('name_es')
                                            ->label(__('fields.name_es'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('type_en')
                                            ->label(__('fields.type_en'))
                                            ->maxLength(255)
                                            ->helperText(__('fields.helper_bilingual_type')),
                                        TextInput::make('type_es')
                                            ->label(__('fields.type_es'))
                                            ->maxLength(255),
                                        Textarea::make('desc_en')
                                            ->label(__('fields.desc_en'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Textarea::make('desc_es')
                                            ->label(__('fields.desc_es'))
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Section::make(__('sections.stats'))
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('stats')
                                            ->label('')
                                            ->simple(
                                                TextInput::make('stat')
                                                    ->label('')
                                                    ->placeholder('+50 Coding Speed / Velocidad de Codificación')
                                                    ->maxLength(255),
                                            )
                                            ->columnSpanFull()
                                            ->addActionLabel(__('app.add_stat'))
                                            ->helperText(__('fields.helper_bilingual_stats')),
                                    ]),
                            ]),

                        Tab::make(__('sections.inventory'))
                            ->icon(Heroicon::OutlinedArchiveBox)
                            ->schema([
                                Grid::make(3)->schema([
                                    Select::make('category_id')
                                        ->label(__('fields.category'))
                                        ->options(fn () => Category::query()->orderBy('position')->get()
                                            ->mapWithKeys(fn (Category $category): array => [
                                                $category->getKey() => $category->name('en'),
                                            ])
                                            ->all())
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->preload(),
                                    TextInput::make('position')
                                        ->label(__('fields.position'))
                                        ->numeric()
                                        ->default(0)
                                        ->helperText(__('fields.helper_position')),
                                    DatePicker::make('acquired_at')
                                        ->label(__('fields.acquired_at'))
                                        ->displayFormat('d/m/Y')
                                        ->native(false),
                                ]),
                            ]),

                        Tab::make(__('sections.equipment'))
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->schema([
                                Section::make('')
                                    ->description(__('sections.equipment_desc'))
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->schema([
                                        Select::make('loadout')
                                            ->label(__('fields.loadout'))
                                            ->options(ItemLoadout::class)
                                            ->placeholder(__('app.none'))
                                            ->native(false)
                                            ->live(),
                                        Select::make('equipment_slot')
                                            ->label(__('fields.equipment_slot'))
                                            ->options(EquipmentSlot::class)
                                            ->placeholder(__('app.none'))
                                            ->native(false)
                                            ->visible(fn (Get $get): bool => filled($get('loadout'))),
                                    ]),
                            ]),

                        Tab::make(__('sections.audit_information'))
                            ->icon(Heroicon::OutlinedClock)
                            ->hidden(fn (string $operation): bool => $operation !== 'view')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('creator.name')
                                        ->label(__('fields.created_by'))
                                        ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                    TextInput::make('created_at')
                                        ->label(__('fields.created_at'))
                                        ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                    TextInput::make('editor.name')
                                        ->label(__('fields.updated_by'))
                                        ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                    TextInput::make('updated_at')
                                        ->label(__('fields.updated_at'))
                                        ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                    TextInput::make('destroyer.name')
                                        ->label(__('fields.deleted_by'))
                                        ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                    TextInput::make('deleted_at')
                                        ->label(__('fields.deleted_at'))
                                        ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.name'))
                    ->getStateUsing(fn (Item $record): string => $record->name())
                    ->searchable(query: function ($query, string $search): void {
                        $query->where('slug', 'like', sprintf('%%%s%%', $search))
                            ->orWhereIn('slug', function ($subQuery) use ($search): void {
                                $subQuery->selectRaw("REPLACE(key, '.name', '')")
                                    ->from('language_lines')
                                    ->where('group', LanguageLine::ITEMS_GROUP)
                                    ->where('key', 'like', '%.name')
                                    ->where(function ($q) use ($search): void {
                                        $q->whereRaw("JSON_EXTRACT(text, '$.en') LIKE ?", [sprintf('%%%s%%', $search)])
                                            ->orWhereRaw("JSON_EXTRACT(text, '$.es') LIKE ?", [sprintf('%%%s%%', $search)]);
                                    });
                            });
                    })
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('rarity')
                    ->label(__('fields.rarity'))
                    ->badge()
                    ->color(fn (ItemRarity $state): string => match ($state) {
                        ItemRarity::Common => 'gray',
                        ItemRarity::Rare => 'info',
                        ItemRarity::Epic => 'warning',
                        ItemRarity::Legendary => 'success',
                    })
                    ->formatStateUsing(fn (ItemRarity $state): string => $state->label()),
                TextColumn::make('category_id')
                    ->label(__('fields.category'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state, Item $record): string => $record->category?->name('en') ?? '—')
                    ->color(fn (?string $state, Item $record): ?string => $record->category?->color_token),
                TextColumn::make('loadout')
                    ->label(__('fields.loadout'))
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn (?ItemLoadout $state): string => $state?->label() ?? '—'),
                TextColumn::make('equipment_slot')
                    ->label(__('fields.slot'))
                    ->placeholder('—')
                    ->formatStateUsing(fn (?EquipmentSlot $state): string => $state?->label() ?? '—'),
                TextColumn::make('position')
                    ->label(__('fields.position'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('fields.category'))
                    ->options(fn () => Category::query()->orderBy('position')->get()
                        ->mapWithKeys(fn (Category $category): array => [
                            $category->getKey() => $category->name('en'),
                        ])
                        ->all()),
                SelectFilter::make('rarity')
                    ->options(ItemRarity::class),
                SelectFilter::make('loadout')
                    ->options(ItemLoadout::class)
                    ->placeholder(__('app.all')),
                TrashedFilter::make(),
            ])
            ->defaultSort('position')
            ->recordActions([
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
            'index' => ListItems::route('/'),
        ];
    }

    public static function handleRecordCreation(array $data): Model
    {
        $translations = self::extractTranslations($data);
        unset($data['name_en'], $data['name_es'], $data['type_en'], $data['type_es'], $data['desc_en'], $data['desc_es']);

        /** @var Item $item */
        $item = self::getModel()::query()->create($data);

        resolve(SyncModelTranslationLinesAction::class)->handle(
            $item,
            LanguageLine::ITEMS_GROUP,
            'slug',
            $translations,
        );

        return $item;
    }

    public static function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Item $item */
        $item = $record;

        $previousSlug = (string) $item->getOriginal('slug');
        $translations = self::extractTranslations($data);
        unset($data['name_en'], $data['name_es'], $data['type_en'], $data['type_es'], $data['desc_en'], $data['desc_es']);

        $item->fill($data)->save();

        $action = resolve(SyncModelTranslationLinesAction::class);

        if ($previousSlug !== '' && $previousSlug !== $item->slug) {
            $action->renameKey($item, LanguageLine::ITEMS_GROUP, 'slug', $previousSlug);
        }

        $action->handle($item, LanguageLine::ITEMS_GROUP, 'slug', $translations);

        return $item;
    }

    public static function handleRecordDeletion(Model $record): bool
    {
        /** @var Item $item */
        $item = $record;

        $deleted = (bool) $item->delete();

        if ($deleted) {
            resolve(SyncModelTranslationLinesAction::class)->purge($item, LanguageLine::ITEMS_GROUP, 'slug');
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, array<string, string|null>>
     */
    private static function extractTranslations(array $data): array
    {
        $fields = ['name', 'type', 'desc'];
        $locales = ['en', 'es'];

        $out = [];
        foreach ($fields as $field) {
            $bucket = [];
            foreach ($locales as $locale) {
                $key = sprintf('%s_%s', $field, $locale);
                $bucket[$locale] = isset($data[$key]) && is_string($data[$key]) ? $data[$key] : null;
            }

            $out[$field] = $bucket;
        }

        return $out;
    }
}
