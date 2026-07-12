<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Component;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;
use Spatie\Permission\Support\Config;

final class UserResource extends Resource
{
    #[Override]
    protected static ?string $model = User::class;

    #[Override]
    protected static ?string $tenantOwnershipRelationshipName = 'teams';

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUserGroup;

    #[Override]
    protected static ?int $navigationSort = 901;

    #[Override]
    protected static ?string $recordTitleAttribute = 'email';

    #[Override]
    protected static int $globalSearchResultsLimit = 3;

    public static function getModelLabel(): string
    {
        return __('resources.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.users');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var User $record */
        return $record->email;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['firstname', 'lastname', 'email', 'username'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var User $record */
        return [
            __('fields.username') => $record->username,
            __('fields.name') => $record->firstname . ' ' . $record->lastname,
        ];
    }

    public static function getNavigationGroup(): string
    {
        return __('menu.nav_group.settings');
    }

    public static function getTenantRelationshipName(): string
    {
        return 'members';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('sections.personal_information'))
                            ->icon(Heroicon::OutlinedUser)
                            ->schema([
                                Section::make('')
                                    ->description(__('sections.personal_data_desc'))
                                    ->icon(Heroicon::OutlinedUser)
                                    ->collapsible()
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->schema([
                                        FileUpload::make('avatar_url')
                                            ->label(__('fields.avatar_url'))
                                            ->disk('public')
                                            ->directory('avatars')
                                            ->visibility('public')
                                            ->avatar()
                                            ->image()
                                            ->columnSpanFull(),
                                        TextInput::make('id')
                                            ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                        TextInput::make('firstname')
                                            ->label(__('fields.firstname'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('lastname')
                                            ->label(__('fields.lastname'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('username')
                                            ->label(__('fields.username'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(User::class, 'username', ignoreRecord: true),
                                        TextInput::make('email')
                                            ->label(__('fields.email'))
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(User::class, 'email', ignoreRecord: true),
                                        DatePicker::make('email_verified_at')
                                            ->label(__('fields.email_verified_at'))
                                            ->displayFormat('d/m/Y h:i A')
                                            ->readOnly(fn (string $operation, $state): bool => $operation === 'edit' && ! is_null($state))
                                            ->native(false),
                                        TextInput::make('password')
                                            ->label(__('fields.password'))
                                            ->password()
                                            ->revealable()
                                            ->minLength(8)
                                            ->dehydrated(fn (?string $state): bool => filled($state))
                                            ->required(fn (string $operation): bool => $operation === 'create')
                                            ->hidden(fn (string $operation): bool => $operation === 'view'),
                                        Select::make('roles')
                                            ->label(__('fields.roles'))
                                            ->required()
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->relationship(
                                                'roles',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query): Builder => $query->where(
                                                    $query->getModel()->getTable() . '.' . Config::teamForeignKey(),
                                                    getPermissionsTeamId()
                                                )
                                            )
                                            ->saveRelationshipsUsing(function (User $record, array $state): void {
                                                $record->roles()->syncWithPivotValues($state, [
                                                    Config::teamForeignKey() => getPermissionsTeamId(),
                                                ]);
                                            })
                                            ->helperText(__('fields.helper_select_roles')),
                                    ]),
                            ]),

                        Tab::make(__('sections.audit_information'))
                            ->icon(Heroicon::OutlinedClock)
                            ->hidden(fn (string $operation): bool => $operation !== 'view')
                            ->schema([
                                Section::make('')
                                    ->description(__('sections.audit_information_desc'))
                                    ->icon(Heroicon::OutlinedClock)
                                    ->collapsible()
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->hidden(fn (string $operation): bool => $operation !== 'view')
                                    ->schema([
                                        TextInput::make('creator.name')
                                            ->label(__('fields.created_by'))
                                            ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                        DatePicker::make('created_at')
                                            ->label(__('fields.created_at'))
                                            ->displayFormat('d/m/Y h:i A')
                                            ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                        TextInput::make('editor.name')
                                            ->label(__('fields.updated_by'))
                                            ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                        DatePicker::make('updated_at')
                                            ->label(__('fields.updated_at'))
                                            ->displayFormat('d/m/Y h:i A')
                                            ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                        TextInput::make('destroyer.name')
                                            ->label(__('fields.deleted_by'))
                                            ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                        DatePicker::make('deleted_at')
                                            ->label(__('fields.deleted_at'))
                                            ->displayFormat('d/m/Y h:i A')
                                            ->hidden(fn (string $operation): bool => $operation !== 'view'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActionsAlignment('center')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ActionGroup::make([
                    Action::make('resend_verification_email')
                        ->label(__('app.resend_email'))
                        ->icon(Heroicon::OutlinedEnvelope)
                        ->authorize(fn (User $record): bool => ! $record->hasVerifiedEmail())
                        ->action(function (User $record): void {
                            $notification = new VerifyEmail();
                            $notification->url = filament()->getVerifyEmailUrl($record);

                            $record->notify($notification);
                            Notification::make()
                                ->title(__('app.verification_email_sent'))
                                ->send();
                        })
                        ->requiresConfirmation(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ])->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon(Heroicon::OutlinedUserGroup)
            ->emptyStateHeading(__('resources.empty_state_heading', ['label' => __('resources.user')]))
            ->emptyStateDescription(__('resources.empty_state_description', ['label' => __('resources.user')]))
            ->emptyStateActions([
                Action::make('create')
                    ->label(__('app.create'))
                    ->icon(Heroicon::OutlinedPlus)
                    ->url(self::getUrl('index') . '?tableAction=create')
                    ->button(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['roles']);

        $user = auth()->user();

        if ($user !== null && ! $user->hasRole('admin')) {
            $query->where('id', $user->id);
        }

        return $query;
    }

    /**
     * @return array<int, Column|Component>
     */
    private static function getTableColumns(): array
    {
        return [
            ImageColumn::make('avatar_url')
                ->circular()
                ->label(__('fields.avatar_url'))
                ->imageSize(64)
                ->alignCenter()
                ->verticallyAlignCenter()
                ->getStateUsing(fn (User $record): ?string => $record->getFilamentAvatarUrl()),
            TextColumn::make('firstname')
                ->label(__('fields.name'))
                ->formatStateUsing(fn (User $record): string => $record->firstname . ' ' . $record->lastname)
                ->searchable(['firstname', 'lastname'])
                ->sortable()
                ->weight('bold')
                ->alignCenter()
                ->verticallyAlignCenter()
                ->limit(30)
                ->tooltip(fn (User $record): string => $record->firstname . ' ' . $record->lastname),
            TextColumn::make('email')
                ->label(__('fields.email'))
                ->searchable()
                ->sortable()
                ->icon(Heroicon::OutlinedEnvelope)
                ->size('sm')
                ->alignCenter()
                ->verticallyAlignCenter()
                ->limit(30)
                ->tooltip(fn (User $record): string => $record->email),
            TextColumn::make('roles.name')
                ->label(__('fields.roles'))
                ->badge()
                ->separator(',')
                ->alignCenter()
                ->verticallyAlignCenter()
                ->limitList(3),
        ];
    }
}
