<?php

declare(strict_types=1);

namespace App\Filament\Resources\GitHubRepos\Pages;

use App\Filament\Resources\GitHubRepos\GitHubRepoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditGitHubRepo extends EditRecord
{
    #[Override]
    protected static string $resource = GitHubRepoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
