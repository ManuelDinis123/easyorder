<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search page
     * 
     * @return View
     */
    function index()
    {
        if (!session()->get('authenticated')) return redirect("/");

        return view('frontend.search.search');
    }

    /**
     * Search functionallity for restaurants
     * 
     * @return response
     */
    function search(Request $param)
    {
        
    }
}
