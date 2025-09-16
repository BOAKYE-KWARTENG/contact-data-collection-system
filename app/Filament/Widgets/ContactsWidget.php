<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget\Card;

class ContactsWidget extends BaseWidget
{
    

    protected function getStats(): array
    {
        return [
            Card::make('Total Contacts', Contact::count()),
            Card::make('Male Contacts', Contact::where('gender', 'male')->count()),
            Card::make('Female Contacts', Contact::where('gender', 'female')->count()),
        ];
    }
}
