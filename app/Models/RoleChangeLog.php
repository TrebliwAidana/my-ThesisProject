<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleChangeLog extends Model
{
    protected $fillable = [
        'member_id',
        'changed_by',
        'old_role',
        'new_role',
        'reason',
        'ip_address'
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    
    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
