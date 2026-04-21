<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestoredBackup extends Model
{
    protected $table = 'restored_backups';

    protected $fillable = [
        'backup_filename',
        'backup_hash',
        'restored_by',
        'restored_at',
    ];

    protected $casts = [
        'restored_at' => 'datetime',
    ];

    public $timestamps = false; // we use restored_at manually

    public function restoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by');
    }
}