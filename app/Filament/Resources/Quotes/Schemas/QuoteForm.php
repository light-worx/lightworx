<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'id')
                    ->options(Project::query()->pluck('project', 'id'))
                    ->required(),
                TextInput::make('rate')
                    ->default(function (){
                        return setting('hourly_rate');
                    })
                    ->numeric(),
                TextEntry::make('quotedate')
                    ->hiddenOn('create')
                    ->label('Date invoice sent')
                    ->placeholder('Not sent yet'),
                TextInput::make('total')
                    ->hiddenOn('create')
                    ->readonly(),
            ]);
    }
}
