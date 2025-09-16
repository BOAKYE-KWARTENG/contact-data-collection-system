<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Filament\Imports\UserImporter;


class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User registered')
            ->body('The user has been created successfully.');
    }



    

    // Returns the URL to redirect to the list page after creating a contact
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
