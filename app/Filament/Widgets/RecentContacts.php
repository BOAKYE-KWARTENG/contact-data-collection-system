<?php

namespace App\Filament\Widgets;

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
                Contact::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()           // 👈 renders as a colored badge
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ]);
    }

    
}
