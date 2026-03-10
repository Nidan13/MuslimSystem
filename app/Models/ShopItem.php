<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'price', 'category_id', 'image_url', 'is_active'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
