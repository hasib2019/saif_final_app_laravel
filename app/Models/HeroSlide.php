<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'additional_details',
        'button_text',
        'button_link',
        'image',
        'background_image',
        'video_url',
        'sort_order',
        'is_active',
        'show_overlay',
        'overlay_opacity',
        'text_position',
        'animation_type'
    ];

    protected $casts = [
        'title' => 'array',
        'subtitle' => 'array',
        'description' => 'array',
        'additional_details' => 'array',
        'button_text' => 'array',
        'is_active' => 'boolean',
        'show_overlay' => 'boolean',
        'sort_order' => 'integer',
        'overlay_opacity' => 'integer'
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
