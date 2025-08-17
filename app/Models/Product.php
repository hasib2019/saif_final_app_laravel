<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'specifications',
        'category_id',
        'images',
        'videos',
        'catalog_file',
        'is_active',
        'sort_order'
    ];

    public $translatable = [
        'name',
        'description',
        'specifications'
    ];

    protected $casts = [
        'images' => 'array',
        'videos' => 'array',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
