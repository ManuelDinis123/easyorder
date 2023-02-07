<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavController extends Controller
{
    /**
     * Goes to search page
     * 
     * @return View
     */
    function goToSearch() {
        return view('frontend.search.partialSearch');
    }
}
