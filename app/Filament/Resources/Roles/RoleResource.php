<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles;

use App\Filament\Concerns\HasPermissionFormComponents;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
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
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Override;
use Spatie\Permission\Models\Role;

final class RoleResource extends Resource
{
    use HasPermissionFormComponents;

    #[Override]
    protected static ?string $model = Role::class;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedShieldCheck;

    #[Override]
    protected static ?int $navigationSort = 1001;

    #[Override]
    protected static ?string $modelLabel = 'Perfil';

    #[Override]
    protected static ?string $pluralModelLabel = 'Perfiles';

    private ?string $subheading = 'Aquí puedes gestionar los perfiles de usuario de tu organización';

    public static function getNavigationGroup(): ?string
    {
        return __('menu.nav_group.settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Role Name'))
                                    ->required()
                                    ->trim()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('e.g. admin, editor, user'),
                                TextInput::make('guard_name')
                                    ->label(__('Guard'))
                                    ->default('web')
                                    ->nullable()
                                    ->trim()
                                    ->maxLength(255),
                                self::getSelectAllToggle(),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                self::getPermissionFormComponents(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Id')
                    ->alignCenter()
                    ->verticallyAlignCenter()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('Role'))
                    ->weight(FontWeight::Medium)
                    ->formatStateUsing(fn (string $state): string => Str::headline($state))
                    ->alignCenter()
                    ->verticallyAlignCenter()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guard_name')
                    ->label(__('Guard'))
                    ->badge()
                    ->alignCenter()
                    ->verticallyAlignCenter()
                    ->color('warning'),
                TextColumn::make('permissions_count')
                    ->label(__('Permissions'))
                    ->badge()
                    ->alignCenter()
                    ->verticallyAlignCenter()
                    ->counts('permissions')
                    ->color('primary'),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime('d/m/Y h:i A')
                    ->alignCenter()
                    ->verticallyAlignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function canGloballySearch(): bool
    {
        return false;
    }
}
