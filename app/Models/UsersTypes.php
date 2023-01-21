<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersTypes extends Model
{
    use HasFactory;

    protected $table = "users_types";
    protected $guarded = ['id']; 
    public $timestamps = false;
}
