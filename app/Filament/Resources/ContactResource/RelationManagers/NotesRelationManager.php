<?php

namespace App\Filament\Resources\ContactResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';
    protected static ?string $title       = 'Private Notes';

    // ── Form ──────────────────────────────────────────────

    public function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\RichEditor::make('note')
                ->label('Note')
                ->required()
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'underline',
                    'strike',
                    'bulletList',
                    'orderedList',
                    'link',
                    'undo',
                    'redo',
                ])
                ->placeholder('Write a private note about this contact...')
                ->columnSpanFull(),

            Forms\Components\Hidden::make('user_id')
                ->default(fn () => auth()->id()),

        ]);
    }

    // ── Table ─────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->html()              // ✅ renders RichEditor HTML correctly
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Added By')
                    ->icon('heroicon-o-user-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y • h:i A')
                    ->sortable()
                    ->since(),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Note')
                    ->icon('heroicon-o-pencil-square')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id(); // ✅ always set author
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('No notes yet')
            ->emptyStateDescription('Add a private note to keep track of important details.');
    }

    // ── Permissions ───────────────────────────────────────

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->hasRole('admin')
            || $ownerRecord->user_id === auth()->id();
    }

    public function canEdit(Model $record): bool
    {
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        return $record->user_id === auth()->id()
            && $record->created_at->greaterThan(now()->subMinute());
    }
    // 
    public function canDelete(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
