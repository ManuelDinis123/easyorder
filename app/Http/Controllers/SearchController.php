<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Restaurants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // all restaurants by default
        $results = Restaurants::where("isPublic", 1)
            ->where("active", 1)
            ->get();

        $data = [];
        foreach ($results as $result) {
            $data[] = [
                "id" => $result->id,
                "name" => $result->name,
                "description" => $result->description,
                "logo_url" => $result->logo_url,
                "logo_name" => $result->logo_name
            ];
        }

        return view('frontend.search.search')->with("restaurants", $data)->with("hasSearch", '0');
    }

    /**
     * Search functionallity for restaurants
     * 
     * @return response
     */
    function search(Request $data)
    {
        if (!AppHelper::hasLogin()) return redirect("/");
        $keyword = $data->input('query');

        $results = Restaurants::where('name', 'like', "%$keyword%")->where("isPublic", 1)->where("active", 1)
            ->get();

        $data = [];
        foreach ($results as $result) {
            $data[] = [
                "id" => $result->id,
                "name" => $result->name,
                "description" => $result->description,
                "logo_url" => $result->logo_url,
                "logo_name" => $result->logo_name
            ];
        }

        $hasSearch = '0';
        if(isset($keyword)){
            $hasSearch = '1';            
        }
        return view('frontend.search.partialSearch')->with("restaurants", $data)->with("hasSearch", $hasSearch);
    }
}
