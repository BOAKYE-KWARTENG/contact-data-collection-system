<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReminderResource\Pages;
use App\Filament\Resources\ReminderResource\RelationManagers;
use App\Models\Contact;
use App\Models\Reminder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReminderResource extends Resource
{
    protected static ?string $model = Reminder::class;

    protected static ?string $navigationIcon  = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Reminders';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Reminder Details')
                ->schema([
                    Forms\Components\Select::make('contact_id')
                        ->label('Contact')
                        ->options(function () {
                            // If user is an admin, let them see and select any contact in the CRM
                            if (auth()->user()->hasRole('admin')) {
                                return \App\Models\Contact::pluck('name', 'id');
                            }

                            // If user is a standard representative, restrict to their owned contacts
                            return \App\Models\Contact::where('user_id', auth()->id())
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        // Grey out and disable this field dynamically during edit operations
                        ->disabled(fn (string $operation) => $operation === 'edit')
                        // Ensure the disabled value is still sent to the database backend upon saving
                        ->dehydrated(),

                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Call John Doe, Send proposal'),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->placeholder('Add more details about this reminder...')
                        ->columnSpanFull(),

                    Forms\Components\DatePicker::make('due_date')
                        ->label('Due Date')
                        ->required()
                        ->native(false)
                        ->displayFormat('M d, Y')
                        // Safe fallback ensuring historical reminders don't throw validation locks in modals
                        ->minDate(fn (string $operation, $record) => ($operation === 'create' || $record === null) ? now() : null),

                    Forms\Components\Toggle::make('completed')
                        ->label('Mark as Completed')
                        ->default(false)
                        ->onColor('success')
                        ->offColor('gray'),

                    // Forms\Components\Hidden::make('user_id')
                        // ->default(fn () => auth()->id()),

                ])->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('due_date', 'asc')
            ->columns([

                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->icon('heroicon-o-user')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Reminder')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match(true) {
                        $record->due_date->isToday()    => 'warning',
                        $record->due_date->isPast()     => 'danger',
                        $record->due_date->isFuture()   => 'success',
                        default                          => 'gray',
                    }),

                Tables\Columns\IconColumn::make('completed')
                    ->label('Done')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('completed')
                    ->label('Status')
                    ->options([
                        '0' => 'Pending',
                        '1' => 'Completed',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query) => $query->overdue()),

                Tables\Filters\Filter::make('due_today')
                    ->label('Due Today')
                    ->query(fn (Builder $query) => $query->dueToday()),
            ])
            ->actions([
                // Quick complete toggle
                Tables\Actions\Action::make('toggle_complete')
                    ->label(fn ($record) => $record->completed ? 'Mark Pending' : 'Mark Done')
                    ->icon(fn ($record) => $record->completed
                        ? 'heroicon-o-arrow-uturn-left'
                        : 'heroicon-o-check')
                    ->color(fn ($record) => $record->completed ? 'gray' : 'success')
                    ->action(fn ($record) => $record->update([
                        'completed' => !$record->completed
                    ])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }




    // ── Query Scopes ───────────────────────────────────────

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('admin')) {
            return $query;
        }

        // Isolate reminders safely by checking ownership of the parent contact
        return $query->whereHas('contact', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }




    // ── Permissions ───────────────────────────────────────

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('admin')
            || $record->contact->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('admin')
            || $record->contact->user_id === auth()->id();
    }





    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReminders::route('/'),
            'create' => Pages\CreateReminder::route('/create'),
            'edit' => Pages\EditReminder::route('/{record}/edit'),
        ];
    }
}
