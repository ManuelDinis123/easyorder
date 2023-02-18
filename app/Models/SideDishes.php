<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SideDishes extends Model
{
    use HasFactory;


    protected $table = "side_dishes";
    protected $guarded = ['id'];
    public $timestamps = false;
}
