<?php

namespace App\Filament\Resources\Quotes\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DisbursementsRelationManager extends RelationManager
{
    protected static string $relationship = 'disbursements';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('disbursementdate'),
                TextInput::make('disbursable_id')
                    ->required()
                    ->numeric(),
                TextInput::make('disbursable_type'),
                TextInput::make('details')
                    ->required(),
                TextInput::make('disbursement')
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('disbursementdate')
                    ->date()
                    ->sortable(),
                TextColumn::make('disbursable_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('disbursable_type')
                    ->searchable(),
                TextColumn::make('details')
                    ->searchable(),
                TextColumn::make('disbursement')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
