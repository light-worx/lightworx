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

class HoursRelationManager extends RelationManager
{
    protected static string $relationship = 'hours';

    protected static ?string $title = 'Charges';

    protected static ?string $modelLabel = 'Charge';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('hourdate')->label('Date')
                    ->default(now())
                    ->required(),
                TextInput::make('details')
                    ->required(),
                TextInput::make('hours')
                    ->default(1)
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('details')
            ->columns([
                TextColumn::make('hourdate')->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('details')
                    ->searchable(),
                TextColumn::make('hours')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->label('New charge')
                ->after(function (Model $record) {
                    $parent=Invoice::with('hours','disbursements')->where('id',$record->hourable_id)->first();
                    $total=0;
                    foreach ($parent->hours as $hour){
                        $total=$total+($hour->hours*$parent->rate);
                    }
                    foreach ($parent->disbursements as $disbursement){
                        $total=$total+($disbursement->disbursement);
                    }
                    $parent->total=$total;
                    $parent->save();
                    $this->dispatch('refresh-total');
                })
            ])
            ->recordActions([
                EditAction::make()
                ->after(function (Model $record) {
                    $parent=Invoice::with('hours','disbursements')->where('id',$record->hourable_id)->first();
                    $total=0;
                    foreach ($parent->hours as $hour){
                        $total=$total+($hour->hours*$parent->rate);
                    }
                    foreach ($parent->disbursements as $disbursement){
                        $total=$total+($disbursement->disbursement);
                    }
                    $parent->total=$total;
                    $parent->save();
                    $this->dispatch('refresh-total');
                }),
                DeleteAction::make()
                ->after(function (Model $record) {
                    $parent=Invoice::with('hours','disbursements')->where('id',$record->hourable_id)->first();
                    $total=0;
                    foreach ($parent->hours as $hour){
                        $total=$total+($hour->hours*$parent->rate);
                    }
                    foreach ($parent->disbursements as $disbursement){
                        $total=$total+($disbursement->disbursement);
                    }
                    $parent->total=$total;
                    $parent->save();
                    $this->dispatch('refresh-total');
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
