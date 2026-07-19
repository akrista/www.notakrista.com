<?php

declare(strict_types=1);

namespace App\Filament\Resources\Translations;

use App\Filament\Resources\Translations\Pages\ListTranslations;
use App\Models\LanguageLine;
use App\Models\Locale;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;

final class TranslationResource extends Resource
{
    /** @var ?class-string<LanguageLine> */
    #[Override]
    protected static ?string $model = LanguageLine::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedLanguage;

    #[Override]
    protected static ?string $recordTitleAttribute = 'key';

    #[Override]
    protected static ?int $navigationSort = 902;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.translation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.translations');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.settings');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['group', 'key'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var LanguageLine $record */
        $text = is_array($record->text) ? $record->text : [];

        return [
            __('fields.translation_group') => $record->group,
            __('fields.translation_text_value_label') => (string) ($text[app()->getLocale()] ?? $text['en'] ?? '—'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.translation_identity'))
                    ->description(__('sections.translation_identity_desc'))
                    ->icon(Heroicon::OutlinedIdentification)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('group')
                            ->label(__('fields.translation_group'))
                            ->required()
                            ->trim()
                            ->maxLength(255)
                            ->placeholder('resources'),
                        TextInput::make('key')
                            ->label(__('fields.translation_key'))
                            ->required()
                            ->trim()
                            ->maxLength(255)
                            ->placeholder('user'),
                    ]),

                Section::make(__('sections.translation_values'))
                    ->description(__('sections.translation_values_desc'))
                    ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('text')
                            ->label('')
                            ->schema([
                                Select::make('locale')
                                    ->label(__('fields.translation_text_key_label'))
                                    ->options(fn (): array => self::localeOptions())
                                    ->required()
                                    ->distinct()
                                    ->fixIndistinctState()
                                    ->selectablePlaceholder(false),
                                TextInput::make('value')
                                    ->label(__('fields.translation_text_value_label'))
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel(__('fields.translation_add'))
                            ->helperText(__('fields.translation_text_helper'))
                            ->columnSpanFull()
                            ->reorderable(false)
                            ->required()
                            ->afterStateHydrated(static function (Repeater $component, ?array $state): void {
                                if (! is_array($state)) {
                                    $component->state([]);

                                    return;
                                }

                                if (isset($state[0]) && is_array($state[0]) && array_key_exists('locale', $state[0])) {
                                    return;
                                }

                                $repeaterState = [];
                                foreach ($state as $locale => $value) {
                                    $repeaterState[] = [
                                        'locale' => $locale,
                                        'value' => $value,
                                    ];
                                }

                                $component->state($repeaterState);
                            })
                            ->mutateDehydratedStateUsing(static function (?array $state): array {
                                if (! is_array($state)) {
                                    return [];
                                }

                                $dbState = [];
                                foreach ($state as $item) {
                                    $locale = $item['locale'] ?? null;
                                    $value = $item['value'] ?? null;

                                    if ($locale !== null && $locale !== '') {
                                        $dbState[$locale] = $value;
                                    }
                                }

                                return $dbState;
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->label(__('fields.translation_group'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->label(__('fields.translation_key'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->placeholder('—'),
                TextColumn::make('text_preview')
                    ->label(__('fields.translation_text_value_label'))
                    ->getStateUsing(fn (LanguageLine $record): string => self::previewTranslation($record, app()->getLocale()))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('fields.translation_group'))
                    ->options(fn (): array => self::groupOptions())
                    ->searchable()
                    ->native(false),
                Filter::make('has_multiple_locales')
                    ->label(__('fields.translation_text'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereJsonLength('text', '>', 1)),
            ])
            ->defaultSort('group')
            ->recordActions([
                ViewAction::make(),
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
            'index' => ListTranslations::route('/'),
        ];
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    /**
     * @return Builder<LanguageLine>
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<LanguageLine> $query */
        $query = parent::getEloquentQuery();

        return $query
            ->orderBy('group')
            ->orderBy('key');
    }

    /**
     * @return array<string, string>
     */
    private static function groupOptions(): array
    {
        /** @var array<string, string> $groups */
        $groups = LanguageLine::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group', 'group')
            ->all();

        return $groups;
    }

    /**
     * @return array<string, string>
     */
    private static function localeOptions(): array
    {
        return Locale::query()
            ->active()
            ->ordered()
            ->get()
            ->mapWithKeys(fn (Locale $locale): array => [
                $locale->code => sprintf('%s (%s)', $locale->native_name, $locale->code),
            ])
            ->all();
    }

    private static function previewTranslation(LanguageLine $record, string $locale): string
    {
        $text = is_array($record->text) ? $record->text : [];

        $preferred = $text[$locale] ?? $text['en'] ?? reset($text);

        if (! is_string($preferred) || $preferred === '') {
            return '';
        }

        return mb_strlen($preferred) > 80 ? mb_substr($preferred, 0, 77) . '…' : $preferred;
    }
}
