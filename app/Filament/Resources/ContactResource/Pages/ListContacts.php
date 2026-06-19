<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\ContactsWidget;

use Filament\Resources\Components\Tab; // Import the Tab component for creating tabs
use Illuminate\Database\Eloquent\Builder; // Import the Builder class for query building
use App\Filament\Imports\ProductImporter;
use EightyNine\ExcelImport\ExcelImportAction;



class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
                Actions\CreateAction::make(),
        ];

    }


    


    protected function getHeaderWidgets(): array
    {
        return [
            ContactsWidget::class,
        ];
    }


    public function getTabs(): array
    {
        return [
            'All'        => Tab::make(), //->query(fn (Builder $query) => $query->whereNull('deleted_at')),
            'MTN'        => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('telco', 'MTN')),
            'Telecel'    => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('telco', 'Telecel')),
            'AirtelTigo' => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('telco', 'AirtelTigo')),
            'Glo'        => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('telco', 'Glo')),
            'Male'       => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('gender', 'male')),
            'Female'     => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('gender', 'female')),
            'Other'      => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('gender', 'other')),
            'Single'     => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'single')),
            'Married'    => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'married')),
            'Divorced'   => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'divorced')),
            'Widowed'    => Tab::make()->query(fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'widowed')),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'All'; // Set the default active tab to 'Widowed'
    }
}


