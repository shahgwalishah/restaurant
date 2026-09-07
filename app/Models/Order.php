<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model { protected $guarded = []; protected $casts = ['subtotal'=>'float','discount'=>'float','tax'=>'float','total'=>'float']; public function items(){ return $this->hasMany(OrderItem::class); } public function table(){ return $this->belongsTo(RestaurantTable::class,'restaurant_table_id'); } public function customer(){ return $this->belongsTo(Customer::class); } }
