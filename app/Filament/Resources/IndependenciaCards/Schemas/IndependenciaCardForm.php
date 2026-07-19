<?php

declare(strict_types=1);

namespace App\Filament\Resources\IndependenciaCards\Schemas;

use App\Models\IndependenciaCard;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class IndependenciaCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('card_summary')
                    ->label('Card Summary')
                    ->content(
                        fn (?IndependenciaCard $record): ?string => $record instanceof IndependenciaCard
                        ? sprintf(
                            'Deck: %s | Card #%s | %s | ATQ: %d / DEF: %d',
                            $record->deck,
                            $record->card_id,
                            ucfirst($record->type),
                            $record->attack,
                            $record->defense
                        )
                        : null
                    )
                    ->hiddenOn('create')
                    ->columnSpanFull(),

                Tabs::make('IndependenciaCardTabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General Info')
                            ->schema([
                                Grid::make(3)->schema([
                                    Select::make('deck')
                                        ->options([
                                            '1' => 'Deck 1',
                                            '2' => 'Deck 2',
                                        ])
                                        ->required()
                                        ->native(false),
                                    TextInput::make('card_id')
                                        ->label('Card ID / Number')
                                        ->required()
                                        ->maxLength(10),
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('type')
                                        ->options([
                                            'magic' => 'Magic',
                                            'trap' => 'Trap',
                                            'fire' => 'Fire',
                                            'water' => 'Water',
                                            'agua' => 'Agua',
                                            'earth' => 'Earth',
                                            'light' => 'Light',
                                            'dark' => 'Dark',
                                        ])
                                        ->required()
                                        ->native(false),
                                    TextInput::make('stars')
                                        ->label('Stars (0–12)')
                                        ->numeric()
                                        ->extraInputAttributes(['type' => 'range', 'min' => 0, 'max' => 12, 'step' => 1])
                                        ->default(0)
                                        ->required(),
                                ]),
                                Grid::make(2)->schema([
                                    TextInput::make('monster_type')
                                        ->maxLength(255)
                                        ->placeholder('e.g. Prócer de la Independencia'),
                                    TextInput::make('new_monster_type')
                                        ->maxLength(255)
                                        ->placeholder('e.g. hero, warrior'),
                                ]),
                            ]),

                        Tab::make('Combat Stats')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('attack')
                                        ->label('Attack (ATQ)')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                    TextInput::make('defense')
                                        ->label('Defense (DEF)')
                                        ->numeric()
                                        ->default(0)
                                        ->required(),
                                ]),
                            ]),

                        Tab::make('Description & Effect')
                            ->schema([
                                Textarea::make('description')
                                    ->rows(4)
                                    ->placeholder('Historical background or description...'),
                                Textarea::make('effect')
                                    ->rows(4)
                                    ->placeholder('Gameplay effect text...'),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
