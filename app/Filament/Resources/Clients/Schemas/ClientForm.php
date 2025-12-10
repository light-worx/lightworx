<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client')
                    ->required(),
                TextInput::make('contact_email')
                    ->email()
                    ->required(),
                TextInput::make('contact_firstname')
                    ->required(),
                TextInput::make('contact_surname')
                    ->required()
            ]);
    }
}
