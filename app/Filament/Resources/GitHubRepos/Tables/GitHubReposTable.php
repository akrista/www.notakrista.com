<?php

declare(strict_types=1);

namespace App\Filament\Resources\GitHubRepos\Tables;

use App\Models\GitHubRepo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class GitHubReposTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Repository')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->url(fn ($record): string => $record->html_url)
                    ->openUrlInNewTab(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('language')
                    ->label('Language')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('stargazers_count')
                    ->label('Stars')
                    ->numeric()
                    ->sortable()
                    ->icon(Heroicon::OutlinedStar),
                TextColumn::make('forks_count')
                    ->label('Forks')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('visibility')
                    ->label('Visibility')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('last_push_at')
                    ->label('Last Push')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('synced_at')
                    ->label('Synced')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('language')
                    ->options(fn (): array => array_combine(
                        GitHubRepo::languages(),
                        GitHubRepo::languages()
                    ))
                    ->searchable(),
                SelectFilter::make('visibility')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
