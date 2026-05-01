<?php

declare(strict_types=1);

namespace App\Filament\Resources\GitHubRepos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class GitHubRepoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Repository Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('full_name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('html_url')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('language')
                            ->maxLength(255),
                        TextInput::make('stargazers_count')
                            ->label('Stars')
                            ->numeric()
                            ->default(0),
                        TextInput::make('forks_count')
                            ->label('Forks')
                            ->numeric()
                            ->default(0),
                        TextInput::make('open_issues_count')
                            ->label('Open Issues')
                            ->numeric()
                            ->default(0),
                        TextInput::make('visibility')
                            ->default('public'),
                    ]),
            ]);
    }
}
