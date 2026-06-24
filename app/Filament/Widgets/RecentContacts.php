<?php

namespace App\Filament\Widgets;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Contact;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Support\Enums\MaxWidth;

class RecentContacts extends BaseWidget
{

    protected static ?string $maxWidth = 'full';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getTableQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()           // renders as a colored badge
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultPaginationPageOption(5)  // Show 5 records per page
            ->paginated([5, 10, 25]);         // Optional: give user page size choices;
    }

    protected function getTableQuery(): Builder
    {
        $query = Contact::whereNull('deleted_at')->latest()->limit(7);

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('team_lead')) {
            return $query;
        }

        return $query->where('user_id', auth()->id());
    }


}
