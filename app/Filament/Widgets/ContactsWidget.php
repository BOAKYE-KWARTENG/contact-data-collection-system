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


        // Admin sees all, users see their own
        $query = auth()->user()->hasRole('admin')
            ? Contact::query()
            : Contact::where('user_id', auth()->id());

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
