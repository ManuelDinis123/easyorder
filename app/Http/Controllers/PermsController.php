<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermsController extends Controller
{
    function index() {
        return view("frontend/professional/admin/perms");
    }
}
