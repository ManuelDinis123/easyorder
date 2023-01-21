<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectRestaurantType extends Model
{
    use HasFactory;

    protected $table = "connect_restaurant_type";
    protected $guarded = ['id']; 
    public $timestamps = false;
}
