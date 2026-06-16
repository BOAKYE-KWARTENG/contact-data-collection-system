<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ContactStatus;
use App\Models\ContactActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ContactNote;



class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function activities(): HasMany
    {
        return $this->hasMany(ContactActivity::class)->latest();
    }

    // add relationship:
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function notes(): HasMany
    {
        return $this->hasMany(ContactNote::class)->latest();
    }

}
