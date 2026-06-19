<?php

namespace App\Imports;

use App\Enums\ContactStatus;
use App\Models\Contact;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class ContactImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsEmptyRows
{
    public int $duplicatesCount = 0;
    public array $duplicateEmails = [];

    // ── Process Each Row ───────────────────────────────────

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $email = strtolower(trim($row['email']));

            // ✅ Check for duplicate email in DB
            if (Contact::where('email', $email)->exists()) {
                $this->duplicatesCount++;
                $this->duplicateEmails[] = $email;
                continue; // skip duplicate, don't import
            }

            Contact::create([
                'user_id'        => Auth::id(),  // ✅ logged in user
                'name'           => trim($row['name']),
                'gender'         => strtolower(trim($row['gender'])),
                'age_range'      => trim($row['age_range']),
                'marital_status' => strtolower(trim($row['marital_status'])),
                'mobile_number'  => trim($row['mobile_number']),
                'telco'          => trim($row['telco']),
                'email'          => $email,
                'status' => isset($row['status']) && in_array($row['status'], ['lead', 'prospect', 'customer', 'inactive'])
                    ? $row['status']
                    : ContactStatus::Lead->value,
            ]);
        }
    }

    // ── Validation Rules ───────────────────────────────────

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'gender'         => ['required', Rule::in(['male', 'female', 'other'])],
            'age_range'      => ['required', 'string'],
            'marital_status' => ['required', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'mobile_number'  => ['required', 'string', 'max:255'],
            'telco'          => ['required', Rule::in(['MTN', 'Telecel', 'AirtelTigo', 'Glo'])],
            'email'          => ['required', 'email'],
            'status' => ['nullable', Rule::in(['lead', 'prospect', 'customer', 'inactive'])],
        ];
    }

    // ── Custom Validation Messages ─────────────────────────

    public function customValidationMessages(): array
    {
        return [
            'name.required'           => 'The name column is required.',
            'email.required'          => 'The email column is required.',
            'email.email'             => 'Row contains an invalid email address.',
            'gender.in'               => 'Gender must be male, female or other.',
            'marital_status.in'       => 'Marital status must be single, married, divorced or widowed.',
            'telco.in'                => 'Telco must be MTN, Telecel, AirtelTigo or Glo.',
        ];
    }

    // ── Performance Optimisations ──────────────────────────

    public function batchSize(): int
    {
        return 500; // inserts 500 rows at a time
    }

    public function chunkSize(): int
    {
        return 500; // reads 500 rows at a time
    }
}