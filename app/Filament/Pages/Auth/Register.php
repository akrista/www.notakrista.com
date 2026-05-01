<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class Register extends \Filament\Auth\Pages\Register
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getUsernameFormComponent(),
                $this->getFirstnameFormComponent(),
                $this->getLastnameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label(__('filament-panels::auth/pages/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    private function getUsernameFormComponent(): TextInput
    {
        return TextInput::make('username')
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->validationMessages([
                'unique' => 'Username already registered',
            ])
            ->autofocus()
            ->autocomplete(false);
    }

    private function getFirstnameFormComponent(): TextInput
    {
        return TextInput::make('firstname')
            ->required()
            ->maxLength(255);
    }

    private function getLastnameFormComponent(): TextInput
    {
        return TextInput::make('lastname')
            ->required()
            ->maxLength(255);
    }
}
