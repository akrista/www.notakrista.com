<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Actions\Teams\CreateTeam;
use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use RuntimeException;

final class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('app.new_team');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.team_name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $createTeam = resolve(CreateTeam::class);

        $user = auth()->user();
        $name = is_string($data['name'] ?? null) ? $data['name'] : '';

        throw_if($user === null, RuntimeException::class, 'User must be authenticated to register a team.');

        return $createTeam->handle($user, $name, isPersonal: false);
    }
}
