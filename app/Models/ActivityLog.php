<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;


    // ⚡ Performance & Integrity Boosting: Logs are immutable historical writes.
    // Disabling updated_at removes unnecessary query payload overhead.
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description'
    ];


    /**
     * Relationship: Get the user who executed this action.
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Polymorphic Relationship: Links dynamically to the target model (Contact, Reminder, etc.)
     * This avoids data redundancy by keeping an active relationship map to the original entity.
     */
    
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
