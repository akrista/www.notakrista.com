<?php

declare(strict_types=1);

namespace App\Filament\Resources\IndependenciaCards;

use App\Filament\Resources\IndependenciaCards\Pages\CreateIndependenciaCard;
use App\Filament\Resources\IndependenciaCards\Pages\EditIndependenciaCard;
use App\Filament\Resources\IndependenciaCards\Pages\ListIndependenciaCards;
use App\Filament\Resources\IndependenciaCards\Schemas\IndependenciaCardForm;
use App\Filament\Resources\IndependenciaCards\Tables\IndependenciaCardsTable;
use App\Models\IndependenciaCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;

class IndependenciaCardResource extends Resource
{
    #[Override]
    protected static ?string $model = IndependenciaCard::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCreditCard;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    protected static ?int $navigationSort = 900;

    #[Override]
    protected static bool $isScopedToTenant = false;

    public static function getModelLabel(): string
    {
        return __('resources.independencia_card');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.independencia_cards');
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.inventory');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'card_id'];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var IndependenciaCard $record */
        return [
            'Deck' => $record->deck,
            'Card ID' => $record->card_id,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return IndependenciaCardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndependenciaCardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIndependenciaCards::route('/'),
            'create' => CreateIndependenciaCard::route('/create'),
            'edit' => EditIndependenciaCard::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
