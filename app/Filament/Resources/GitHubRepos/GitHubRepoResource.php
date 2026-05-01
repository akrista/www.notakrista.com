<?php

declare(strict_types=1);

namespace App\Filament\Resources\GitHubRepos;

use App\Filament\Resources\GitHubRepos\Pages\CreateGitHubRepo;
use App\Filament\Resources\GitHubRepos\Pages\EditGitHubRepo;
use App\Filament\Resources\GitHubRepos\Pages\ListGitHubRepos;
use App\Filament\Resources\GitHubRepos\Schemas\GitHubRepoForm;
use App\Filament\Resources\GitHubRepos\Tables\GitHubReposTable;
use App\Models\GitHubRepo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class GitHubRepoResource extends Resource
{
    #[Override]
    protected static ?string $model = GitHubRepo::class;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    #[Override]
    protected static string | UnitEnum | null $navigationGroup = 'Content';

    #[Override]
    protected static ?int $navigationSort = 100;

    #[Override]
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return number_format(GitHubRepo::query()->count());
    }

    public static function form(Schema $schema): Schema
    {
        return GitHubRepoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GitHubReposTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGitHubRepos::route('/'),
            'create' => CreateGitHubRepo::route('/create'),
            'edit' => EditGitHubRepo::route('/{record}/edit'),
        ];
    }
}
