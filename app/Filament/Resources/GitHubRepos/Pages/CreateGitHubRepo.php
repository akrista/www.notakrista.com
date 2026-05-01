<?php

declare(strict_types=1);

namespace App\Filament\Resources\GitHubRepos\Pages;

use App\Filament\Resources\GitHubRepos\GitHubRepoResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateGitHubRepo extends CreateRecord
{
    #[Override]
    protected static string $resource = GitHubRepoResource::class;
}
