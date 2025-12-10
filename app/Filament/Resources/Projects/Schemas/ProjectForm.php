<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('project')
                    ->required(),
                Select::make('client_id')
                    ->relationship('client', 'id')
                    ->options(Client::query()->pluck('client', 'id'))
                    ->required(),
                Toggle::make('active')
                    ->required()
                    ->default(true),
            ]);
    }
}
