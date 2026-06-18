<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
        ];
    }


    

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User updated')
            ->body('The user has been saved successfully.');
    }

    // Added authorization check to ensure users can only edit their own contacts
    protected function authorizeAccess(): void
    {
        $contact = $this->getRecord();

        abort_unless(
            auth()->user()->hasRole('admin') || $contact->user_id === auth()->id(),
            403,
            'You can only edit your own contacts.'
        );
    }



    // Automatically redirects back to the contact table view after a successful save
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    
}
