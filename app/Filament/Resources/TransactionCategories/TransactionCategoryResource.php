<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionCategories;

use App\Filament\Resources\TransactionCategories\Pages\ListTransactionCategories;
use App\Models\TransactionCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Override;

final class TransactionCategoryResource extends Resource
{
    #[Override]
    protected static ?string $model = TransactionCategory::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTag;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    protected static ?int $navigationSort = 700;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.transaction_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.transaction_categories');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.budget');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->label(__('fields.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(TransactionCategory::class, 'slug', ignoreRecord: true)
                            ->helperText(__('fields.helper_slug'))
                            ->columnSpanFull(),
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
                        Toggle::make('is_archived')
                            ->label(__('fields.is_archived'))
                            ->inline(false)
                            ->default(false),
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
                    ->weight('bold'),
                TextColumn::make('slug')
                    ->label(__('fields.slug'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('color_token')
                    ->label(__('fields.color_token'))
                    ->placeholder('muted')
                    ->toggleable(),
                IconColumn::make('is_archived')
                    ->label(__('fields.is_archived'))
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('position')
                    ->label(__('fields.position'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('position')
            ->filters([
                TernaryFilter::make('is_archived')
                    ->label(__('fields.is_archived')),
            ])
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
            'index' => ListTransactionCategories::route('/'),
        ];
    }
}
