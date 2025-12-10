<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Client;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

protected $listeners = ['refresh-total' => 'refreshTotal'];

    public function refreshTotal()
    {
        $this->form->fill(['total' => $this->record->fresh()->total]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Send quotation')
                ->hidden(!is_null($this->record->quotedate))
                ->icon('heroicon-o-envelope')
                ->action(function () {
                    $this->record->quotedate=date('Y-m-d');
                    $this->record->save();
                    $client=Client::find($this->record->client_id);
                    $client->account=$client->account+$this->record->total;
                    $client->save();
                    /*$subject = 'New service: ' . $this->record->servicetime . " " . $this->record->servicedate;
                    $body = "Hi" . $fname . "<br><br>Just to let you know that a new service has been added to the database.<br><br>It can be accessed <a href=\"" . url('/') . "/admin/worship/services/" . $this->record->id . "/edit\">here</a><br><br>Thank you!";
                    Mail::html($body, function ($message) use ($email, $subject) {
                        $message->to($email)->subject($subject);
                        $message->from(setting('email.church_email'),setting('general.church_name'));
                    });*/
                    Notification::make('Email sent')->title('Quotation emailed to ' . $this->record->client->client)->send();
            }),
            Action::make('Quotation')
                ->icon('heroicon-o-document-text')
                ->url(function (Quote $record){
                    return route('quote', ['id' => $record]);
                }),
            DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            route('filament.admin.resources.quotes.index') => 'Quotes',
            route('filament.admin.resources.quotes.edit', $record) => 'Quote ' . $record->id,
            null => 'Edit',
        ];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return 'Edit Quote ' . $record->id;
    }
}
