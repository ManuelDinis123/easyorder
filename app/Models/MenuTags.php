<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuTags extends Model
{
    use HasFactory;

    protected $table = "menu_tags";
    protected $guarded = ['id']; 
    public $timestamps = false;
}
