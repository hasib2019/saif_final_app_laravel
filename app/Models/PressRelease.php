<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PressRelease extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'content',
        'link',
        'image',
        'published_at',
        'is_active'
    ];

    public $translatable = [
        'title',
        'description',
        'content'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
