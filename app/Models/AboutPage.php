<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutPage extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'mission',
        'vision',
        'values',
        'history',
        'team_description',
        'about_image',
        'team_image',
        'office_images',
        'founded_year',
        'employees_count',
        'countries_served',
        'projects_completed',
        'achievements',
        'timeline',
        'is_active'
    ];

    public $translatable = [
        'title',
        'subtitle',
        'content',
        'mission',
        'vision',
        'values',
        'history',
        'team_description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'office_images' => 'array',
        'achievements' => 'array',
        'timeline' => 'array'
    ];
}
