<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $table = 'contact_info';
    
    protected $fillable = [
        'email',
        'phone',
        'address',
        'business_hours',
        'map_latitude',
        'map_longitude',
        'map_embed_code',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
