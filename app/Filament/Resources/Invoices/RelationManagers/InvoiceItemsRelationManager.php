<?php

namespace App\Filament\Resources\Invoices\RelationManagers;

use App\Models\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InvoiceItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoiceitems';

    protected static ?string $title = 'Invoice items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('itemdate')->label('Date')
                    ->default(now())
                    ->required(),
                TextInput::make('details')
                    ->required(),
                TextInput::make('quantity')
                    ->default(1)
                    ->required()
                    ->numeric(),
                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        if ($state === null) {
                            $ownerRate = $this->getOwnerRecord()->rate;
                            if ($ownerRate > 0) {
                                $component->state($ownerRate);
                            } else {
                                $component->state(setting('hourly_rate'));
                            }
                        }
                    })
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('details')
            ->columns([
                TextColumn::make('itemdate')->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('details')
                    ->searchable(),
                TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
