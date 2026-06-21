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
        // 1. Initialize mandatory default global "All" tab
        $tabs = [
            'All' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('deleted_at')),
        ];

        // 2. Loop and append the Database Telcos dynamically
        try {
            $telcos = \App\Models\Telco::orderBy('name')->get();

            foreach ($telcos as $telco) {
                // Using a prefix key like 'telco_' prevents collision with other tab identifiers
                $tabs['telco_' . $telco->code] = Tab::make($telco->name)
                    ->modifyQueryUsing(fn (Builder $query) => $query
                        ->whereNull('deleted_at')
                        ->where('telco_id', $telco->id) // ✅ Relational lookup matching your new database structure
                    );
            }
        } catch (\Exception $e) {
            // Fallback gracefully if database or migrations aren't fully completed yet
        }

        // 3. Append your static demographic layout groupings
        $staticDemographics = [
            // Gender Filters
            'Male'     => fn (Builder $query) => $query->whereNull('deleted_at')->where('gender', 'male'),
            'Female'   => fn (Builder $query) => $query->whereNull('deleted_at')->where('gender', 'female'),
            'Other'    => fn (Builder $query) => $query->whereNull('deleted_at')->where('gender', 'other'),
            
            // Marital Status Filters
            'Single'   => fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'single'),
            'Married'  => fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'married'),
            'Divorced' => fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'divorced'),
            'Widowed'  => fn (Builder $query) => $query->whereNull('deleted_at')->where('marital_status', 'widowed'),
        ];

        foreach ($staticDemographics as $label => $queryCallback) {
            $tabs[$label] = Tab::make($label)->modifyQueryUsing($queryCallback);
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'All'; // Set the default active tab to 'Widowed'
    }
}


