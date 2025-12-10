<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Client;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function afterCreate(): void
    {
        $client = Client::find($this->record->client_id);
        if ($client) {
            $client->account = $client->account - $this->record->amount;
            $client->save();
        }
    }
}
