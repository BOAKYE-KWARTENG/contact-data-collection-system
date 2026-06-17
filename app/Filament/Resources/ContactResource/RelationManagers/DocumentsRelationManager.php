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
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title       = 'Documents & Attachments';

    // ── Form ──────────────────────────────────────────────
    public function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Select::make('name')
                ->label('Document Type')
                ->required()
                ->options([
                    'Contract' => 'Contract',
                    'National ID' => 'National ID',
                    'Quotation' => 'Quotation',
                ])
                ->placeholder('Select a document type'),

            Forms\Components\FileUpload::make('file_path')
                ->label('Upload File')
                ->directory('contact-documents')
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/jpg',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->maxSize(10240) // 10MB
                ->required()
                ->columnSpanFull()
                // Auto-capture file metadata on upload
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set('file_type', $state->getMimeType());
                        $set('file_size', $state->getSize());
                    }
                }),

            Forms\Components\Hidden::make('user_id')
                ->default(fn () => auth()->id()),

            Forms\Components\Hidden::make('file_type'),
            Forms\Components\Hidden::make('file_size'),

        ]);
    }


    // ── Table ─────────────────────────────────────────────
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->label('Document Name')
                    ->icon('heroicon-o-document')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match(true) {
                        str_contains($state, 'pdf')  => 'PDF',
                        str_contains($state, 'image') => 'Image',
                        str_contains($state, 'word') => 'Word',
                        default                       => strtoupper($state),
                    })
                    ->color(fn ($state) => match(true) {
                        str_contains($state ?? '', 'pdf')   => 'danger',
                        str_contains($state ?? '', 'image') => 'success',
                        str_contains($state ?? '', 'word')  => 'info',
                        default                              => 'gray',
                    }),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '—';
                        $units = ['B', 'KB', 'MB', 'GB'];
                        $unit  = 0;
                        while ($state >= 1024 && $unit < 3) {
                            $state /= 1024;
                            $unit++;
                        }
                        return round($state, 2) . ' ' . $units[$unit];
                    }),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->icon('heroicon-o-user-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M d, Y • h:i A')
                    ->sortable()
                    ->since(),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload Document')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                // ✅ Download action
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        // ✅ Delete file from storage when record is deleted
                        Storage::disk('public')->delete($record->file_path);
                    }),
            ])
            ->emptyStateIcon('heroicon-o-paper-clip')
            ->emptyStateHeading('No documents yet')
            ->emptyStateDescription('Upload contracts, IDs, quotations or any relevant files.');
    }


    // ── Permissions ─────────────────────────────────────── 

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->hasRole('admin')
            || $ownerRecord->user_id === auth()->id();
    }

    public function canDelete(Model $record): bool
    {
        return auth()->user()->hasRole('admin')
            || $record->user_id === auth()->id();
    }

}
