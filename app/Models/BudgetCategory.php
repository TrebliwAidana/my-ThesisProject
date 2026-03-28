<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'allocated_amount',
        'used_amount',
        'is_active',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'used_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}