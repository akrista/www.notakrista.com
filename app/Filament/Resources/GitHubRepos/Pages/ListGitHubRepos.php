<?php

declare(strict_types=1);

namespace App\Filament\Resources\GitHubRepos\Pages;

use App\Filament\Resources\GitHubRepos\GitHubRepoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListGitHubRepos extends ListRecords
{
    #[Override]
    protected static string $resource = GitHubRepoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
