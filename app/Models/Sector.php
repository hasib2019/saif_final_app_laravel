<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'image',
        'use_cases',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'use_cases' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
