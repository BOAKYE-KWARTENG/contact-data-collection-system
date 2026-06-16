<?php

namespace App\Filament\Resources\ContactResource\RelationManagers;


use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\ActivityType; // Import the ActivityType enum
use Illuminate\Database\Eloquent\Model;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title       = 'Activity Timeline';

    // Form (Add / Edit Activity)

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('activity_type')
                    ->label('Activity Type')
                    ->options(
                        collect(ActivityType::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                            ->toArray()
                    )
                    ->required()
                    ->native(false),

                Forms\Components\Textarea::make('description')
                    ->label('Notes / Description')
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Add details about this activity...')
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => auth()->id()),
            ]);
    }

    // Override the create action so the logged-in user is always recorded automatically:
    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->id();

        return parent::handleRecordCreation($data);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('activity_type')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Activity')
                    ->badge()
                    
                    ->formatStateUsing(fn ($state) => $state instanceof ActivityType
                        ? $state->getLabel()
                        : $state
                    )
                    ->color(fn ($state) => $state instanceof ActivityType
                        ? $state->getColor()
                        : 'gray'
                    )
                    ->icon(fn ($state) => $state instanceof ActivityType
                        ? $state->getIcon()
                        : null
                    )

                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Notes')
                    ->wrap()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Logged By')
                    ->icon('heroicon-o-user-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('M d, Y • h:i A')
                    ->sortable()
                    ->since(), 
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Log Activity')
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading('No activities yet')
            ->emptyStateDescription('Start tracking interactions by logging the first activity.')

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Allow creating activities on own contacts
    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->hasRole('admin')
            || $ownerRecord->user_id === auth()->id();
    }
    
    // Allow editing & deleting own activity logs
    public function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $isWithinOneMinute = $record->created_at
            && $record->created_at->greaterThan(now()->subMinute());

        return $isWithinOneMinute && (
            auth()->user()->hasRole('admin')
            || $record->created_by === auth()->id()
        );
    }

    public function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }
    
}
