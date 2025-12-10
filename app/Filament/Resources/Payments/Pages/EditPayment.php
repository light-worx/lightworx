<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Client;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected $previousAmount;
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Save the original amount before update
        $this->previousAmount = $this->record->amount;

        return $data;
    }

    protected function afterSave(): void
    {
        $client = Client::find($this->record->client_id);
        if ($client) {
            $difference = $this->record->amount - $this->previousAmount;
            $client->account = $client->account - $difference;
            $client->save();
        }
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            route('filament.admin.resources.payments.index') => 'Payments',
            route('filament.admin.resources.payments.edit', $record) => $record->client->client . ' payment ',
            null => 'Edit',
        ];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return 'Edit payment from ' . $record->client->client;
    }
}
