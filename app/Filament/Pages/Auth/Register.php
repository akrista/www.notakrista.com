<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Actions\Teams\CreateTeam;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class Register extends \Filament\Auth\Pages\Register
{
    public function mount(): void
    {
        parent::mount();

        if (config('app.env') !== 'production') {
            $this->form->fill([
                'username' => 'devuser',
                'firstname' => 'Developer',
                'lastname' => 'User',
                'email' => 'dev@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]);
        }
    }

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

    protected function handleRegistration(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'username' => $data['username'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $createTeam = resolve(CreateTeam::class);
            $createTeam->handle(
                $user,
                __('app.personal_team', [
                    'name' => sprintf(
                        '%s %s',
                        is_string($data['firstname'] ?? null) ? $data['firstname'] : '',
                        is_string($data['lastname'] ?? null) ? $data['lastname'] : '',
                    ),
                ]),
                isPersonal: true,
            );

            return $user;
        });
    }

    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label(__('filament-panels::auth/pages/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->validationMessages([
                'unique' => __('app.validation_email_taken'),
            ]);
    }

    private function getUsernameFormComponent(): TextInput
    {
        return TextInput::make('username')
            ->label(__('fields.username'))
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->validationMessages([
                'unique' => __('app.validation_username_taken'),
            ])
            ->autofocus()
            ->autocomplete(false);
    }

    private function getFirstnameFormComponent(): TextInput
    {
        return TextInput::make('firstname')
            ->label(__('app.first_name'))
            ->required()
            ->maxLength(255);
    }

    private function getLastnameFormComponent(): TextInput
    {
        return TextInput::make('lastname')
            ->label(__('app.last_name'))
            ->required()
            ->maxLength(255);
    }
}
