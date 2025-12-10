<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('setting')
                    ->required(),
                Select::make('category')
                    ->options(['general'=>'General'])
                    ->default('general')
                    ->selectablePlaceholder(false)
                    ->required(),
                TextInput::make('value')
                    ->required(),
            ]);
    }
}
