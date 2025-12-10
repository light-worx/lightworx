<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Models\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UnsentInvoices extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Invoice::whereNull('invoicedate'))
            ->emptyStateHeading('No unsent invoices')
            ->emptyStateIcon('heroicon-o-inbox')
            ->columns([
                TextColumn::make('project.client.client'),
                TextColumn::make('total')
                    ->numeric()
                    ->prefix('R ')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->recordUrl(
                fn (Invoice $record): string => EditInvoice::getUrl([$record->id]),
            )
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
