<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TelcoResource\Pages;
use App\Filament\Resources\TelcoResource\RelationManagers;
use App\Models\Telco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class TelcoResource extends Resource
{
    protected static ?string $model = Telco::class;

    // This assigns the sidebar icon navigation layout
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    
    // Grouping your navigation links keeps your sidebar organized
    protected static ?string $navigationGroup = 'System Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Carrier Name')
                            ->required()
                            ->unique(Telco::class, 'name', ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                $set('code', Str::slug($state))
                            ),

                        Forms\Components\TextInput::make('code')
                            ->label('Technical Code')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->unique(Telco::class, 'code', ignoreRecord: true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Carrier Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Technical Code')
                    ->fontFamily('mono')
                    ->sortable(),

                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Total Contacts')
                    ->counts('contacts') // Dynamically counts assigned contacts
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created On')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTelcos::route('/'),
            'create' => Pages\CreateTelco::route('/create'),
            'edit' => Pages\EditTelco::route('/{record}/edit'),
        ];
    }
}
