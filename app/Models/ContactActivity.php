<?php

namespace App\Models;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Factories\HasFactory; // add this import
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactActivity extends Model
{
    use HasFactory; // 👈 add

    protected $fillable = [
        'contact_id',
        'created_by',
        'activity_type',
        'description',
    ];

    protected $casts = [
        'activity_type' => ActivityType::class,
    ];

    // ── Relationships ──────────────────────────────────────

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}