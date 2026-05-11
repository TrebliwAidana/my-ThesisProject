<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'version_number',
        'file_path',
        'cloudinary_public_id', // ✅ added
        'file_name',
        'mime_type',
        'file_size',
        'change_notes',
        'uploaded_by',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'file_size'      => 'integer',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDownloadUrlAttribute(): string
    {
        // ✅ If Cloudinary URL exists, use it directly
        if ($this->file_path && str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        // Fallback to route-based download
        return route('documents.version.download', [$this->document_id, $this->id]);
    }
}