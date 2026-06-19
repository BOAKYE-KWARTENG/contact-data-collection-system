<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactImportTemplateExport;

class GenerateContactImportTemplate extends Command
{
    protected $signature   = 'contacts:template';
    protected $description = 'Generate a sample Excel template for contact imports';

    public function handle(): void
    {
        Excel::store(
            new ContactImportTemplateExport(),
            'contact-import-template.xlsx',
            'public'
        );

        $this->info('Template saved to storage/app/public/contact-import-template.xlsx');
    }
}