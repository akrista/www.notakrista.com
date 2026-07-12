<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class EditProfile extends BaseEditProfile
{
    public function mount(): void
    {
        parent::mount();

        if (Filament::getTenant() === null && auth()->check()) {
            $user = auth()->user();
            if ($user !== null) {
                $tenant = $user->currentTeam ?? $user->personalTeam() ?? $user->teams()->first();
                if ($tenant) {
                    Filament::setTenant($tenant);
                }
            }
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('avatar_url')
                    ->label(__('fields.avatar'))
                    ->avatar()
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->columnSpan(2),
                TextInput::make('username')
                    ->label(__('fields.username'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => __('app.validation_username_taken'),
                    ])
                    ->columnSpan(2),
                TextInput::make('firstname')
                    ->label(__('app.first_name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('lastname')
                    ->label(__('app.last_name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
                    ->columnSpan(2)
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => __('app.validation_email_taken'),
                    ]),
                $this->getPasswordFormComponent()->columnSpan(2),
                $this->getPasswordConfirmationFormComponent()->columnSpan(2),
            ])
            ->columns(2);
    }
}
