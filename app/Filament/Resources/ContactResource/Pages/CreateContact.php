<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Filament\Imports\UserImporter;



use App\Models\ActivityLog;




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


    /**
     * Runs automatically right after the database successfully saves the new contact row.
     */
    protected function afterCreate(): void
    {
        // $this->record gives us access to the exact Contact model instance that was just made
        $contact = $this->record;

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'created',
            'subject_type' => $contact->getMorphClass(),
            'subject_id'   => $contact->id,
            'description'  => (auth()->user()?->name ?? 'System') . " created contact: " . $contact->name,
        ]);
    }

}
