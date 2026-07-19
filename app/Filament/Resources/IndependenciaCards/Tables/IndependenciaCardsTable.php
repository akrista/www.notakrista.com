<?php

declare(strict_types=1);

namespace App\Filament\Resources\IndependenciaCards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class IndependenciaCardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('deck')
                    ->label('Deck')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'info',
                        '2' => 'warning',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('card_id')
                    ->label('Card #')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'magic' => 'success',
                        'trap' => 'danger',
                        'fire' => 'warning',
                        'water', 'agua' => 'info',
                        'light' => 'gray',
                        'dark' => 'primary',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stars')
                    ->label('Stars')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('attack')
                    ->label('ATQ')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('defense')
                    ->label('DEF')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('deck')
                    ->options([
                        '1' => 'Deck 1',
                        '2' => 'Deck 2',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'magic' => 'Magic',
                        'trap' => 'Trap',
                        'fire' => 'Fire',
                        'water' => 'Water',
                        'agua' => 'Agua',
                        'earth' => 'Earth',
                        'light' => 'Light',
                        'dark' => 'Dark',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
