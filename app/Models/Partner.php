<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'website_url',
        'is_active',
        'sort_order'
    ];

    public $translatable = [
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
