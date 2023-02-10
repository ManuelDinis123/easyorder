<?php

namespace App\Http\Controllers;

use App\Models\Restaurants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NavController extends Controller
{
    /**
     * Goes to search page
     * 
     * @return View
     */
    function goToSearch(Request $filters)
    {
        if (isset($filters->hasFilters)) {
            // TODO: when ratings are added the filter must work here
            $tags_raw = json_decode($filters->tags);

            $tags = [];
            foreach ($tags_raw as $tag) {
                $tags[] = $tag->value;
            }

            $keyword = $filters->input('query');

            $results = Restaurants::select(
                "restaurants.id",
                "restaurants.name",
                "restaurants.description",
                "restaurants.logo_url",
                "restaurants.logo_name",
            )->join('menu_tags', 'restaurants.id', '=', 'menu_tags.id_restaurant')
                ->where("isPublic", 1)                
                ->where('restaurants.name', 'like', "%$keyword%")                
                ->whereIn('menu_tags.tag', $tags)
                ->distinct()
                ->get();
        } else {
            // all restaurants by default
            $results = Restaurants::where("isPublic", 1)
                ->get();
        }

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

        return view('frontend.search.partialSearch')->with("restaurants", $data);
    }
}
