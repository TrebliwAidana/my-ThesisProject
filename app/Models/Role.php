<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    // A role has many permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}