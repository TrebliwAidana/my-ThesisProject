<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'file_path', 'file_name', 'mime_type',
        'size', 'category', 'uploaded_by', 'organization_id', 'is_public', 'status'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'size' => 'integer',
    ];

    // Relationships
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }


    // Helper: formatted size
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Scope for user's organization or public
    public function scopeAccessible($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('is_public', true)
              ->orWhere('organization_id', $user->organization_id);
        });
    }
}