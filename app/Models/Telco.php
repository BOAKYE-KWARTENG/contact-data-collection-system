<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Telco extends Model
{
    use HasFactory;

    // Mass-assignment protection guardrails
    protected $fillable = ['name', 'code'];

    /**
     * A Telco carrier has many customer contacts assigned to it.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}