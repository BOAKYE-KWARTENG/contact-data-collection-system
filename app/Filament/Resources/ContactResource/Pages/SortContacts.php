<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Resources\Pages\Page;

class SortContacts extends Page
{
    protected static string $resource = ContactResource::class;

    protected static string $view = 'filament.resources.contact-resource.pages.sort-contacts';
}
