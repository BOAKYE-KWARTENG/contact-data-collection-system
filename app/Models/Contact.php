<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ContactStatus;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'age_range',
        'marital_status',
        'mobile_number',
        'telco',
        'status',
        'email',
    ];

    protected $casts = [
        'status' => ContactStatus::class, // 👈 cast to enum
    ];
}
