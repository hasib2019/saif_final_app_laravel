<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnologyFeature extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'category',
        'benefits',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'benefits' => 'array',
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
