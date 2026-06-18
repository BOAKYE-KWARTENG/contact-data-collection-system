<?php

namespace App\Filament\Widgets;

use App\Models\Reminder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;


class UpcomingReminders extends BaseWidget
{
    // Make the widget span the full width of the dashboard row
    protected int | string | array $columnSpan = 'full';

    // Set the title displayed on the dashboard
    protected static ?string $heading = 'Upcoming Reminders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reminder::query()
                    ->upcoming() // Filters uncompleted & due >= today
                    ->when(!auth()->user()->hasRole('admin'), function (Builder $query) {
                        // If NOT an admin, restrict reminders to contacts owned by the current user
                        $query->whereHas('contact', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                    })
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Reminder')
                    ->wrap(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->badge()
                    ->color(fn ($record) => $record->due_date->isPast() ? 'danger' : 'warning')
                    ->formatStateUsing(function ($state) {
                        if ($state->isToday()) {
                            return 'Today';
                        }
                        if ($state->isTomorrow()) {
                            return 'Tomorrow';
                        }
                        // Returns relative time string like "3 days from now"
                        return $state->diffForHumans(['parts' => 1, 'append' => false]);
                    }),
            ])
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25])
            ->actions([
                Tables\Actions\EditAction::make('edit')
                    ->label('Manage')
                    ->icon('heroicon-o-pencil-square')
                    // Pass the static form method from the resource directly
                    ->form(fn (Form $form) => \App\Filament\Resources\ReminderResource::form($form)),
            ]);
    }
}

