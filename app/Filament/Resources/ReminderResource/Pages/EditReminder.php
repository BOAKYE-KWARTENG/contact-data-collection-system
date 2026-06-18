<?php

namespace App\Filament\Resources\ReminderResource\Pages;

use App\Filament\Resources\ReminderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReminder extends EditRecord
{
    protected static string $resource = ReminderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    // Redirect to the list view after editing a reminder
    protected function getRedirectUrl(): string
    {

        // Check if the URL contains our custom 'redirect_to' query parameter
        if (request()->query('redirect_to') === 'dashboard') {
            return url('/admin'); // Redirect back to your dashboard path
        }
        return $this->getResource()::getUrl('index');
    }


}
