<?php

namespace App\Filament\Widgets;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // --- General Stats ---
            Stat::make('Total Contacts', Contact::count())
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Male Contacts', Contact::where('gender', 'male')->count())
                ->color('info')
                ->icon('heroicon-o-user'),

            Stat::make('Female Contacts', Contact::where('gender', 'female')->count())
                ->color('warning')
                ->icon('heroicon-o-user'),

            // --- Status Stats ---
            //Stat::make('Leads', Contact::where('status', ContactStatus::Lead)->count())
               // ->color('info')
                // ->icon('heroicon-o-star'),

            //Stat::make('Prospects', Contact::where('status', ContactStatus::Prospect)->count())
                //->color('warning')
                // ->icon('heroicon-o-eye'),

            //Stat::make('Customers', Contact::where('status', ContactStatus::Customer)->count())
                // ->color('success')
                // ->icon('heroicon-o-check-circle'),

            //Stat::make('Inactive', Contact::where('status', ContactStatus::Inactive)->count())
                // ->color('gray')
                // ->icon('heroicon-o-x-circle'),
        ];
    }
}
