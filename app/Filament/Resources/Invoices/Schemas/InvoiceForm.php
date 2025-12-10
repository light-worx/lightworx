<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'id')
                    ->options(Project::query()->pluck('project', 'id'))
                    ->required(),
                TextInput::make('rate')->label('Hourly rate')
                    ->numeric()
                    ->default(function (){
                        return setting('hourly_rate');
                    }),
                TextEntry::make('invoicedate')
                    ->hiddenOn('create')
                    ->label('Date invoice sent')
                    ->placeholder('Not sent yet'),
                TextInput::make('total')
                    ->hiddenOn('create')
                    ->readonly(),
            ]);
    }
}
