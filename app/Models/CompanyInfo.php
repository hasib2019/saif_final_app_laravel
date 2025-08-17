<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CompanyInfo extends Model
{
    use HasTranslations;

    protected $fillable = [
        'mission',
        'history',
        'values',
        'initiatives',
        'logo',
        'is_active',
        'vision',
        'about_us',
        'team_description',
        'company_overview',
        'about_image',
        'team_image',
        'office_images',
        'founded_year',
        'employees_count',
        'countries_served',
        'projects_completed',
        'achievements',
        'certifications',
        'awards'
    ];

    public $translatable = [
        'mission',
        'history',
        'values',
        'initiatives',
        'vision',
        'about_us',
        'team_description',
        'company_overview'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'office_images' => 'array',
        'achievements' => 'array',
        'certifications' => 'array',
        'awards' => 'array',
        'founded_year' => 'integer',
        'employees_count' => 'integer',
        'countries_served' => 'integer',
        'projects_completed' => 'integer'
    ];
}
