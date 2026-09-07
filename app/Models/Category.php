<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model { protected $guarded = []; protected $casts = ['active'=>'boolean']; public function items(){ return $this->hasMany(MenuItem::class); } }
