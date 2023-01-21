<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRestaurant extends Model
{
    use HasFactory;

    protected $table = "user_restaurant";
    protected $guarded = ['id']; 
    public $timestamps = false;
}
