<?php

namespace App\Models;

use App\Services\CloudinaryService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'document_category_id',
        'tags',
        'owner_id',
        'current_version_id',
        'uploaded_at',
    ];

    protected $casts = [
        'tags'        => 'array',
        'deleted_at'  => 'datetime',
        'uploaded_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderBy('version_number', 'desc');
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function financialTransactions(): MorphToMany
    {
        return $this->morphedByMany(FinancialTransaction::class, 'attachable', 'attachments');
    }

    public function uploader(): BelongsTo
    {
        return $this->owner();
    }

    // -------------------------------------------------------------------------
    // ✅ Updated addVersion() — Cloudinary instead of local disk
    // -------------------------------------------------------------------------

    public function addVersion(
        string $fileUrl,        // Cloudinary secure URL
        string $publicId,       // Cloudinary public_id (for deletion)
        ?string $changeNotes = null,
        int $fileSize = 0,
        string $fileName = '',
        string $mimeType = '',
    ): DocumentVersion {
        $nextVersion = $this->versions()->max('version_number') + 1;

        $version = $this->versions()->create([
            'version_number'       => $nextVersion,
            'file_path'            => $fileUrl,    // ← Cloudinary URL
            'cloudinary_public_id' => $publicId,   // ← for deletion later
            'file_name'            => $fileName,
            'mime_type'            => $mimeType,
            'file_size'            => $fileSize,
            'change_notes'         => $changeNotes,
            'uploaded_by'          => auth()->id(),
        ]);

        $this->update(['current_version_id' => $version->id]);

        return $version;
    }

    // -------------------------------------------------------------------------
    // Formatted file size helper
    // -------------------------------------------------------------------------

    public function getFormattedSizeAttribute(): string
    {
        if (! $this->currentVersion) {
            return '0 B';
        }

        $bytes = $this->currentVersion->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // -------------------------------------------------------------------------
    // ✅ Updated booted() — delete from Cloudinary instead of local disk
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::deleting(function (Document $document) {
            $cloudinary = new CloudinaryService();

            foreach ($document->versions as $version) {
                // ✅ Delete from Cloudinary if public_id exists
                if ($version->cloudinary_public_id) {
                    $cloudinary->delete($version->cloudinary_public_id);
                }
                $version->delete();
            }

            $document->financialTransactions()->detach();
        });
    }
}