<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProductCategory extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'is_active',
        'sort_order'
    ];

    public $translatable = [
        'name',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
