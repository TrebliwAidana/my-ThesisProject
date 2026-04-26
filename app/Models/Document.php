<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;
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
    ];

    protected $casts = [
        'tags'      => 'array',
        'deleted_at' => 'datetime',
    ];

    // Relationship to category
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

    public function addVersion($file, ?string $changeNotes = null): DocumentVersion
    {
        $nextVersion = $this->versions()->max('version_number') + 1;
        $path = $file->store("documents/{$this->id}", 'private');

        $version = $this->versions()->create([
            'version_number' => $nextVersion,
            'file_path'      => $path,
            'file_name'      => $file->getClientOriginalName(),
            'mime_type'      => $file->getMimeType(),
            'file_size'      => $file->getSize(),
            'change_notes'   => $changeNotes,
            'uploaded_by'    => auth()->id(),
        ]);

        $this->update(['current_version_id' => $version->id]);
        return $version;
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->currentVersion) {
            return '0 B';
        }
        $bytes = $this->currentVersion->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    protected static function booted(): void
    {
        static::deleting(function (Document $document) {
            foreach ($document->versions as $version) {
                Storage::disk('private')->delete($version->file_path);
                $version->delete();
            }
            $document->financialTransactions()->detach();
        });
    }

    public function uploader(): BelongsTo
    {
        return $this->owner();
    }
}