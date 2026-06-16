<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role; // add this


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                    ->helperText('Leave blank to keep current password when editing.'),


                // 👇 Role selector — added here
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->options(
                        Role::all()->pluck('name', 'name') // Fetch all roles from the database
                    )
                    ->native(false)
                    ->preload()
                    ->required()
                    ->helperText('Assign a role to control what this user can access.')
                    // 👇 these two lines load & save the Spatie relationship correctly
                    ->afterStateHydrated(function ($component, $record) {
                        if ($record) {
                            $component->state($record->roles->pluck('name')->first());
                        }
                    })
                    // Save the role on create & edit
                    ->saveRelationshipsUsing(function ($component, $record) {
                        $record->syncRoles([$component->getState()]);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->sortable()->searchable(),
                
                // Role badge column — added here
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',  // red
                        'user'  => 'success', // green
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
            ])
            ->filters([
                // 👇 Filter table by role
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filter by Role')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }


    
    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }



    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }



    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

}
