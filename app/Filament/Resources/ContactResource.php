<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Filament\Resources\ContactResource\RelationManagers\ActivitiesRelationManager;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\TernaryFilter;

use App\Filament\Resources\ContactResource\RelationManagers\NotesRelationManager; // 
use App\Filament\Resources\ContactResource\RelationManagers\DocumentsRelationManager; // added for documents relation manager


use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions\CreateAction;
use App\Enums\ContactStatus;



use App\Imports\ContactImport;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;


class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    // Restrict query to current user's contacts only ──

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class, 
            ]);                              
                                        

        if (auth()->user()->hasRole('admin')) {
            return $query;
        }

        return $query->where('user_id', auth()->id());
    }

    



    protected static ?string $label = 'Customer Contact';

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact Information')
                    ->description('Please provide the contact details below.')
                    ->schema([

                        // Hidden field — auto-sets to logged-in user
                         Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),

                
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('gender')
                            ->required()
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ]),

                        Forms\Components\Select::make('age_range')
                            ->required()
                            ->options([
                                '0-17' => '0-17',
                                '18-24' => '18-24',
                                '25-34' => '25-34',
                                '35-44' => '35-44',
                                '45-54' => '45-54',
                                '55-64' => '55-64',
                                '65+' => '65+',
                            ]),

                        Forms\Components\Select::make('marital_status')
                            ->required()
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'divorced' => 'Divorced',
                                'widowed' => 'Widowed',
                            ]),

                        Forms\Components\TextInput::make('mobile_number')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('telco')
                            ->required()
                            ->options([
                                'MTN' => 'MTN',
                                'Telecel' => 'Telecel',
                                'AirtelTigo' => 'AirtelTigo',
                                'Glo' => 'Glo',
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(fn () => collect(ContactStatus::cases())->pluck('name', 'value')->toArray())
                            ->default(ContactStatus::Lead)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2), // Optional: use 2-column layout inside section
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('gender'),
                //Tables\Columns\TextColumn::make('age_range'),
                Tables\Columns\TextColumn::make('marital_status'),
                Tables\Columns\TextColumn::make('mobile_number')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('telco'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->badge()           // 👈 renders as a colored badge
                    ->sortable()
                    ->searchable(),
                    
                //Tables\Columns\TextColumn::make('email')
                    //->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            
            
            ->filters([

                // Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(
                        collect(ContactStatus::cases())->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])->toArray()
                    )
                    ->multiple()        // allow filtering by multiple statuses
                    ->preload(),
                Tables\Filters\SelectFilter::make('marital_status')
                    ->options([
                        'single' => 'Single',
                        'married' => 'Married',
                        'divorced' => 'Divorced',
                        'widowed' => 'Widowed',
                    ]),
                Tables\Filters\SelectFilter::make('telco')
                    ->options([
                        'MTN' => 'MTN',
                        'Telecel' => 'Telecel',
                        'AirtelTigo' => 'AirtelTigo',
                        'Glo' => 'Glo',
                    ]),

                TernaryFilter::make('has_email')
                    ->label('Has Email')
                    ->trueLabel('Yes')
                    ->falseLabel('No')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('email'),
                        false: fn ($query) => $query->whereNull('email'),
                    ),

                Tables\Filters\TrashedFilter::make(),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->label('Edit')
                        // ->slideOver(true)
                        ,
                    // Tables\Actions\DeleteAction::make()
                      //  ->icon('heroicon-o-trash')
                       // ->label('Delete')
                       // ->slideOver(true),
                        
                    
                    // Tables\Actions\ForceDeleteAction::make(),
                    // Tables\Actions\RestoreAction::make(),

                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->label('View')
                        ->slideOver()
                        ,

                    // 👇 Soft delete becomes "Move to Trash"
                    Tables\Actions\DeleteAction::make()
                        ->label('Move to Trash'),

                    // 👇 Restore from trash
                    Tables\Actions\RestoreAction::make(),

                    // 👇 Permanently delete (only visible on trashed records)
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Delete Permanently'),
                ]),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Move to Trash'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Delete Permanently'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restore'),
                ]),
            ])
            
            ->headerActions([

                ExportAction::make()
                    ->label('Export Contacts')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-up-tray'),


                // ── Download Template ──────────────────────────
                Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(Storage::url('contact-import-template.xlsx'))
                    ->openUrlInNewTab(),

                // ── Import Contacts ────────────────────────────
                Action::make('import')
                    ->label('Import Contacts')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('Excel or CSV File')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'text/csv',
                                'text/plain',
                            ])
                            ->maxSize(10240) // 10MB
                            ->required()
                            ->helperText('Download the template above to see the required format.'),
                    ])
                    ->action(function (array $data) {
                        $file = storage_path(
                            'app/public/' . $data['file']
                        );

                        $import = new ContactImport();

                        Excel::import($import, $file);

                        // ── Feedback to user ───────────────────
                        $imported   = \App\Models\Contact::where('user_id', auth()->id())->count();
                        $duplicates = $import->duplicatesCount;
                        $emails     = implode(', ', $import->duplicateEmails);

                        if ($duplicates > 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Import Completed with Duplicates')
                                ->body("Duplicates skipped ({$duplicates}): {$emails}")
                                ->warning()
                                ->persistent()  // stays until dismissed
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Import Successful')
                                ->body('All contacts imported successfully.')
                                ->success()
                                ->send();
                        }
                    }),
                
            ]);


    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class, // Activities relation manager
            NotesRelationManager::class, // Notes relation manager added here
            DocumentsRelationManager::class, // Documents relation manager added here
        ];
    }
    

   
   

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit'   => Pages\EditContact::route('/{record}/edit'), // Restored to standard EditContact
        ];
    }





    // added
    protected static function isAdmin(): bool
    {
        return (bool) auth()->user()?->hasRole('admin');
    }

    protected static function isUser(): bool
    {
        return (bool) auth()->user()?->hasRole('user');
    }
    



    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['admin', 'user']);
    }



    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['admin', 'user']);
    }


    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('admin')
            || $record->user_id === auth()->id(); 
    }


    // public static function canDelete($record): bool
    // {
       // return (bool) auth()->user()?->hasRole('admin');
    // }


    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('admin')
            || $record->user_id === auth()->id(); // 👈 owner can also trash
    }

    public static function canRestore($record): bool
    {
        return auth()->user()->hasRole('admin')
            || $record->user_id === auth()->id(); // 👈 owner can also restore
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()->hasRole('admin'); // 👈 only admin can permanently delete
    }
    

}
