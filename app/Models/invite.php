<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invite extends Model
{
    use HasFactory;

    protected $table = "invite";
    protected $guarded = ['id']; 
    public $timestamps = false;
}
