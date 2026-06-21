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


        // Only admins, super admins, and team leads see all; other users see only dears
        if (auth()->user()->hasAnyRole(['admin', 'super_admin', 'team_lead'])) {
            $query = Contact::whereNull('deleted_at');
        } else {
            $query = Contact::whereNull('deleted_at')
                ->where('status', ContactStatus::Customer->value); //
        }

        return [
            // --- General Stats ---
            Stat::make('Total Contacts', (clone $query)->count())
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Male Contacts', (clone $query)->where('gender', 'male')->count())
                ->color('info')
                ->icon('heroicon-o-user'),

            Stat::make('Female Contacts', (clone $query)->where('gender', 'female')->count())
                ->color('warning')
                ->icon('heroicon-o-user'),

        ];
    }
}
