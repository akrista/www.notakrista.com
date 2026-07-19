<?php

declare(strict_types=1);

namespace App\Filament\Resources\HomePhrases;

use App\Filament\Resources\HomePhrases\Pages\ListHomePhrases;
use App\Models\LanguageLine;
use App\Models\Locale;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

final class HomePhraseResource extends Resource
{
    #[Override]
    protected static ?string $model = LanguageLine::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSparkles;

    #[Override]
    protected static ?string $recordTitleAttribute = 'key';

    #[Override]
    protected static ?int $navigationSort = 902;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.home_phrase');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.home_phrases');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.settings');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['key'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sections.translation_identity'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('key')
                            ->label(__('fields.translation_key'))
                            ->required()
                            ->maxLength(255)
                            ->unique(LanguageLine::class, 'key', modifyRuleUsing: function ($rule, $record) {
                                $rule->where('group', LanguageLine::HOME_PHRASES_GROUP);

                                return $rule->ignore($record?->getKey());
                            }),
                        Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true)
                            ->inline(false)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('sections.translation_values'))
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
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel(__('fields.translation_add'))
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
                                        'value' => is_string($value) ? $value : '',
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
                                    if (is_string($locale) && $locale !== '') {
                                        $dbState[$locale] = is_string($value) ? $value : null;
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
                TextColumn::make('key')
                    ->label(__('fields.translation_key'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('text.en')
                    ->label(__('fields.name_en'))
                    ->getStateUsing(fn (LanguageLine $record): string => (string) ($record->text['en'] ?? ''))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('text.es')
                    ->label(__('fields.name_es'))
                    ->getStateUsing(fn (LanguageLine $record): string => (string) ($record->text['es'] ?? ''))
                    ->placeholder('—')
                    ->wrap(),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean()
                    ->alignCenter(),
            ])
            ->defaultSort('id')
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
            'index' => ListHomePhrases::route('/'),
        ];
    }

    public static function canGloballySearch(): bool
    {
        return false;
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
            ->where('group', LanguageLine::HOME_PHRASES_GROUP)
            ->ordered();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function handleRecordCreation(array $data): LanguageLine
    {
        $data['group'] = LanguageLine::HOME_PHRASES_GROUP;
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        /** @var LanguageLine $line */
        $line = LanguageLine::query()->create($data);

        return $line;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function handleRecordUpdate(LanguageLine $record, array $data): LanguageLine
    {
        $record->fill($data);
        $record->save();

        return $record;
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
}
