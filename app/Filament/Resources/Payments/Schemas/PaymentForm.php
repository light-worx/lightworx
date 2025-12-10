<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('paymentdate')
                    ->default(now())
                    ->required(),
                Select::make('client_id')
                    ->default(fn () => request()->integer('client_id'))
                    ->relationship('client', 'id')
                    ->options(Client::query()->pluck('client', 'id'))
                    ->required(),
                TextInput::make('amount')
                    ->numeric(),
            ]);
    }
}
