<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('firstname')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('lastname')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
                    ->columnSpan(2)
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                $this->getPasswordFormComponent()->columnSpan(2),
                $this->getPasswordConfirmationFormComponent()->columnSpan(2),
            ])
            ->columns(2);
    }
}
