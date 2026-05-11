<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentBackup extends Model
{
    protected $fillable = [
        'filename',
        'cloudinary_url',
        'cloudinary_public_id',
        'category_slug',
        'category_label',
        'file_type',
        'document_count',
        'financial_count',
        'file_count',
        'size_bytes',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}