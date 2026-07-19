<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories;

use App\Actions\Translations\SyncModelTranslationLinesAction;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use App\Models\LanguageLine;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

final class CategoryResource extends Resource
{
    #[Override]
    protected static ?string $model = Category::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTag;

    #[Override]
    protected static ?string $recordTitleAttribute = 'slug';

    #[Override]
    protected static ?int $navigationSort = 701;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.categories');
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
        /** @var Category $record */
        return [
            __('fields.icon') => $record->icon ?? '—',
            __('fields.position') => (string) $record->position,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('slug')
                        ->label(__('fields.slug'))
                        ->required()
                        ->maxLength(255)
                        ->unique(Category::class, 'slug', ignoreRecord: true)
                        ->helperText(__('fields.helper_slug')),
                    TextInput::make('position')
                        ->label(__('fields.position'))
                        ->numeric()
                        ->default(0)
                        ->helperText(__('fields.helper_position')),
                    TextInput::make('icon')
                        ->label(__('fields.icon'))
                        ->helperText(__('fields.helper_icon_emoji'))
                        ->maxLength(16),
                    TextInput::make('color_token')
                        ->label(__('fields.color_token'))
                        ->helperText(__('fields.helper_color_token'))
                        ->maxLength(32)
                        ->placeholder('muted'),
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
                    ->getStateUsing(fn (Category $record): string => $record->name('en'))
                    ->searchable(query: function ($query, string $search): void {
                        $query->where('slug', 'like', sprintf('%%%s%%', $search))
                            ->orWhereIn('slug', function ($subQuery) use ($search): void {
                                $subQuery->select('key')
                                    ->from('language_lines')
                                    ->where('group', LanguageLine::CATEGORIES_GROUP)
                                    ->where(function ($q) use ($search): void {
                                        $q->whereRaw("JSON_EXTRACT(text, '$.en') LIKE ?", [sprintf('%%%s%%', $search)])
                                            ->orWhereRaw("JSON_EXTRACT(text, '$.es') LIKE ?", [sprintf('%%%s%%', $search)]);
                                    });
                            });
                    })
                    ->weight('bold'),
                TextColumn::make('slug')
                    ->label(__('fields.slug'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('color_token')
                    ->label(__('fields.color_token'))
                    ->badge()
                    ->color(fn (?string $state): ?string => $state),
                TextColumn::make('position')
                    ->label(__('fields.position'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
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
            'index' => ListCategories::route('/'),
        ];
    }

    public static function handleRecordCreation(array $data): Model
    {
        $translations = self::extractTranslations($data);
        unset($data['name_en'], $data['name_es']);

        /** @var Category $category */
        $category = self::getModel()::query()->create($data);

        resolve(SyncModelTranslationLinesAction::class)->handle(
            $category,
            LanguageLine::CATEGORIES_GROUP,
            'slug',
            $translations,
        );

        return $category;
    }

    public static function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Category $category */
        $category = $record;

        $previousSlug = (string) $category->getOriginal('slug');
        $translations = self::extractTranslations($data);
        unset($data['name_en'], $data['name_es']);

        $category->fill($data)->save();

        $action = resolve(SyncModelTranslationLinesAction::class);

        if ($previousSlug !== '' && $previousSlug !== $category->slug) {
            $action->renameKey($category, LanguageLine::CATEGORIES_GROUP, 'slug', $previousSlug);
        }

        $action->handle($category, LanguageLine::CATEGORIES_GROUP, 'slug', $translations);

        return $category;
    }

    public static function handleRecordDeletion(Model $record): bool
    {
        /** @var Category $category */
        $category = $record;

        $deleted = (bool) $category->delete();

        if ($deleted) {
            resolve(SyncModelTranslationLinesAction::class)->purge($category, LanguageLine::CATEGORIES_GROUP, 'slug');
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, array<string, string|null>>
     */
    private static function extractTranslations(array $data): array
    {
        return [
            'name' => [
                'en' => isset($data['name_en']) && is_string($data['name_en']) ? $data['name_en'] : null,
                'es' => isset($data['name_es']) && is_string($data['name_es']) ? $data['name_es'] : null,
            ],
        ];
    }
}
