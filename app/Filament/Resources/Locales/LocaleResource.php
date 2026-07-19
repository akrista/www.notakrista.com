<?php

declare(strict_types=1);

namespace App\Filament\Resources\Locales;

use App\Filament\Resources\Locales\Pages\ListLocales;
use App\Models\Locale;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;

final class LocaleResource extends Resource
{
    #[Override]
    protected static ?string $model = Locale::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedLanguage;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    protected static ?int $navigationSort = 901;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.locale');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.locales');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.settings');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'name', 'native_name'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Locale $record */
        return [
            __('fields.locale_code') => $record->code,
            __('fields.locale_direction') => mb_strtoupper($record->direction),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function getDirectionOptions(): array
    {
        return [
            'ltr' => __('app.locale_direction_ltr'),
            'rtl' => __('app.locale_direction_rtl'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('fields.section_locale_identity'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label(__('fields.locale_code'))
                            ->required()
                            ->trim()
                            ->maxLength(12)
                            ->unique(Locale::class, 'code', ignoreRecord: true)
                            ->helperText(__('fields.helper_locale_code'))
                            ->placeholder('en'),
                        TextInput::make('name')
                            ->label(__('fields.locale_name'))
                            ->required()
                            ->trim()
                            ->maxLength(64)
                            ->placeholder('English'),
                        TextInput::make('native_name')
                            ->label(__('fields.locale_native_name'))
                            ->required()
                            ->trim()
                            ->maxLength(64)
                            ->placeholder('English'),
                        Select::make('direction')
                            ->label(__('fields.locale_direction'))
                            ->options(self::getDirectionOptions())
                            ->default('ltr')
                            ->required()
                            ->native(false),
                        TextInput::make('position')
                            ->label(__('fields.position'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('fields.helper_position')),
                        Toggle::make('is_active')
                            ->label(__('fields.is_active'))
                            ->default(true)
                            ->inline(false),
                        Toggle::make('is_default')
                            ->label(__('fields.locale_is_default'))
                            ->default(false)
                            ->inline(false)
                            ->helperText(__('fields.helper_locale_default')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('fields.locale_code'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label(__('fields.locale_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('native_name')
                    ->label(__('fields.locale_native_name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('direction')
                    ->label(__('fields.locale_direction'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'rtl' ? 'warning' : 'muted')
                    ->formatStateUsing(fn (string $state): string => mb_strtoupper($state))
                    ->alignCenter(),
                IconColumn::make('is_default')
                    ->label(__('fields.locale_is_default'))
                    ->boolean()
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('position')
                    ->label(__('fields.position'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->options(self::getDirectionOptions()),
                Filter::make('is_active')
                    ->label(__('fields.is_active'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                TrashedFilter::make(),
            ])
            ->defaultSort('position')
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateDataUsing(function (Locale $record, array $data): array {
                        if ((bool) ($data['is_default'] ?? false)) {
                            Locale::query()
                                ->where('id', '!=', $record->getKey())
                                ->update(['is_default' => false]);
                        }

                        return $data;
                    }),
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
     * @return Builder<Locale>
     */
    #[Override]
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<Locale> $query */
        $query = parent::getEloquentQuery();

        return $query
            ->orderBy('position')
            ->orderBy('name');
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListLocales::route('/'),
        ];
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }
}
