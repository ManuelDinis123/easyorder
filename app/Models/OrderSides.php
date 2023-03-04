<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSides extends Model
{
    use HasFactory;

    protected $table = "order_items_sides";
    protected $guarded = ['id']; 
    public $timestamps = false;
}
