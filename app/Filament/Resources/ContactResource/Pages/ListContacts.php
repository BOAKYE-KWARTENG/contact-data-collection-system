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

            ExcelImportAction::make()

                // ------------------------------------------------------------

                
                // ------------------------------------------------------------

                ->sampleExcel(
                    sampleData: [
                        [
                            'name' => 'John Doe',
                            'gender' => 'male',
                            'age_range' => '25-34',
                            'marital_status' => 'single',
                            'mobile_number' => '0550123456',
                            'telco' => 'MTN',
                            'email' => 'john.doe@example.com',
                        ],
                        [
                            'name' => 'Jane Smith',
                            'gender' => 'female',
                            'age_range' => '35-44',
                            'marital_status' => 'married',
                            'mobile_number' => '0240123456',
                            'telco' => 'Telecel',
                            'email' => 'jane.smith@example.com',
                        ],
                        [
                            'name' => 'Alex Johnson',
                            'gender' => 'other',
                            'age_range' => '18-24',
                            'marital_status' => 'single',
                            'mobile_number' => '0200987654',
                            'telco' => 'AirtelTigo',
                            'email' => 'alex.johnson@example.com',
                        ],
                    ],

                    fileName: 'sample.xlsx',
                    exportClass: App\Exports\SampleExport::class,
                    sampleButtonLabel: 'Download Sample',
                    customiseActionUsing: fn(Action $action) => $action->color('secondary')
                        ->icon('heroicon-m-clipboard')
                        ->requiresConfirmation(),
                )
                ->color('primary')
                ->label('Import Contacts'),


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
            'All' => Tab::make(),
            'MTN' => Tab::make()->query(fn (Builder $query) => $query->where('telco', 'MTN')),
            'Telecel' => Tab::make()->query(fn (Builder $query) => $query->where('telco', 'Telecel')),
            'AirtelTigo' => Tab::make()->query(fn (Builder $query) => $query->where('telco', 'AirtelTigo')),
            'Glo' => Tab::make()->query(fn (Builder $query) => $query->where('telco', 'Glo')),
            'Male' => Tab::make()->query(fn (Builder $query) => $query->where('gender', 'Male')),
            'Female' => Tab::make()->query(fn (Builder $query) => $query->where('gender', 'Female')),
            'Single' => Tab::make()->query(fn (Builder $query) => $query->where('marital_status', 'Single')),
            'Married' => Tab::make()->query(fn (Builder $query) => $query->where('marital_status', 'Married')),
            'Divorced' => Tab::make()->query(fn (Builder $query) => $query->where('marital_status', 'Divorced')),
            'Widowed' => Tab::make()->query(fn (Builder $query) => $query->where('marital_status', 'Widowed')),
        ];
    }


    public function getDefaultActiveTab(): string | int | null
    {
        return 'All'; // Set the default active tab to 'Widowed'
    }
}


