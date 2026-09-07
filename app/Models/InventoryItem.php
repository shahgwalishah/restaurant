<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryItem extends Model { protected $guarded = []; protected $casts = ['stock'=>'float','minimum_stock'=>'float','unit_cost'=>'float']; }
