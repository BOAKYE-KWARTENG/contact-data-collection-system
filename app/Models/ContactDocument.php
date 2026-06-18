<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'user_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];




    
    // Relationships 

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }




    // Helper: Human Readable File Size

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) return '—';

        $units = ['B', 'KB', 'MB', 'GB'];
        $size  = $this->file_size;
        $unit  = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
}