<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Statement')
                ->icon('heroicon-o-document-text')
                ->schema([
                    DatePicker::make('statement_date')
                        ->required()
                        ->default(now())
                ])
                ->action(function (array $data, $record) {
                    return redirect()->route('reports.statement', ['id' => $record, 'date' => $data['statement_date']]);
                }),
            DeleteAction::make(),
        ];
    }
}
