<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Mail\InvoiceMail;
use App\Models\Client;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected $listeners = [
        'refreshInvoiceForm' => '$refresh',
    ];

    public function refreshTotal()
    {
        $this->form->fill(['total' => $this->record->fresh()->total]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Send invoice')
                ->hidden(!is_null($this->record->invoicedate))
                ->icon('heroicon-o-envelope')
                ->requiresConfirmation()
                ->modalHeading('Email Invoice')
                ->modalDescription('Are you sure you want to send this invoice?')
                ->modalSubmitActionLabel('Yes, send it')
                ->action(function () {
                    $this->record->invoicedate = date('Y-m-d');
                    $this->record->save();
                    
                    $client = $this->record->project->client;
                    $client->account = $client->account + $this->record->total;
                    $client->save();                    
                    $attachdata = base64_encode(app('App\Http\Controllers\ReportsController')->invoice($this->record->id, 'Invoice', true));
                    $attachname = 'Invoice_' . $this->record->id . '.pdf';
                    $maildata = [
                        'clientName' => $client->client,
                        'clientEmail' => $client->contact_email,
                        'contactFirstName' => $client->contact_firstname,
                        'contactSurname' => $client->contact_surname,
                        'invoiceId' => $this->record->id,
                        'attachName' => $attachname,
                        'attachData' => $attachdata
                    ];
                    Mail::send(new InvoiceMail($maildata));
                    Notification::make()
                        ->title('Invoice emailed to ' . $client->client)
                        ->success()
                        ->send();
                }),
                
            Action::make('Invoice')
                ->icon('heroicon-o-document-text')
                ->url(function (Invoice $record) {
                    return route('invoice', ['id' => $record]);
                }),
            DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            route('filament.admin.resources.invoices.index') => 'Invoices',
            route('filament.admin.resources.invoices.edit', $record) => 'Invoice ' . $record->id,
            null => 'Edit',
        ];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return 'Edit Invoice ' . $record->id;
    }
}
