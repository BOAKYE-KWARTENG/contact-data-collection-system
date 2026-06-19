<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactImportTemplateExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithColumnWidths
{
    // ── Sample Rows ────────────────────────────────────────

    public function array(): array
    {
        return [
            [
                'John Doe',
                'male',
                '25-34',
                'single',
                '0244123456',
                'MTN',
                'john@example.com',
                'lead',
            ],
            [
                'Jane Smith',
                'female',
                '35-44',
                'married',
                '0557891234',
                'Telecel',
                'jane@example.com',
                'customer',
            ],
            [
                'Bob Johnson',
                'male',
                '45-54',
                'divorced',
                '0261234567',
                'AirtelTigo',
                'bob@example.com',
                'prospect',
            ],
            [
                'Alice Williams',
                'female',
                '25-34',
                'widowed',
                '0244987654',
                'Glo',
                'alice@example.com',
                'inactive',
            ],
        ];
    }

    // ── Column Headers ─────────────────────────────────────

    public function headings(): array
    {
        return [
            'name',
            'gender',
            'age_range',
            'marital_status',
            'mobile_number',
            'telco',
            'email',
            'status',
        ];
    }

    // ── Style Header Row ───────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [ // row 1 = headings
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['argb' => 'FF4F46E5'], // indigo
                ],
            ],
        ];
    }

    // ── Column Widths ──────────────────────────────────────

    public function columnWidths(): array
    {
        return [
            'A' => 20, // name
            'B' => 12, // gender
            'C' => 12, // age_range
            'D' => 16, // marital_status
            'E' => 16, // mobile_number
            'F' => 14, // telco
            'G' => 28, // email
            'H' => 14, // status
        ];
    }
}