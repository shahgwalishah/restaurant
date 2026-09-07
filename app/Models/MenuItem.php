<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'image_path', 'price', 'cost', 'available', 'featured'];

    protected $casts = ['available' => 'boolean', 'featured' => 'boolean', 'price' => 'float', 'cost' => 'float'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
