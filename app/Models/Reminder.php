<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        // 'user_id',
        'title',
        'description',
        'due_date',
        'completed',
    ];

    protected $casts = [
        'due_date'  => 'date',
        'completed' => 'boolean',
    ];



    // Relationships ──────────────────────────────────────

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }



    // Scopes ─────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('completed', false)
                     ->where('due_date', '>=', now()->toDateString())
                     ->orderBy('due_date');
    }

    public function scopeOverdue($query)
    {
        return $query->where('completed', false)
                     ->where('due_date', '<', now()->toDateString());
    }

    public function scopeDueToday($query)
    {
        return $query->where('completed', false)
                     ->whereDate('due_date', today());
    }
}