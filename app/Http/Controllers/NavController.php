<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
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
        $filtersBack = []; // To send back to the view to display the previous filters
        if (isset($filters->hasFilters)) {
            $tags_raw = json_decode($filters->tags);

            $tags = [];
            if ($tags_raw) {
                foreach ($tags_raw as $tag) {
                    $tags[] = $tag->value;
                }
            }

            $filtersBack['tags'] = $tags;

            $stars = [];
            foreach ($filters['reviews'] as $key => $rev) {
                if ($rev) {
                    $stars[] = $key;
                }
            }

            $keyword = $filters->input('query');

            $results = Restaurants::select(
                "restaurants.id",
                "restaurants.name",
                "restaurants.description",
                "restaurants.logo_url",
                "restaurants.logo_name",
            )->join('menu_tags', 'restaurants.id', '=', 'menu_tags.id_restaurant')
                ->join('reviews', 'reviews.restaurant_id', '=', 'restaurants.id')
                ->where("isPublic", 1)
                ->where("active", 1)
                ->where('restaurants.name', 'like', "%$keyword%");

            if (count($tags) > 0) {
                $results = $results->whereIn('menu_tags.tag', $tags);
            }

            if (count($stars) > 0) {
                $results = $results->selectRaw('restaurants.*, ROUND(SUM(reviews.stars) / COUNT(reviews.id)) as avg_stars')
                    ->groupBy('restaurants.id')
                    ->havingRaw('ROUND(SUM(reviews.stars) / COUNT(reviews.id)) IN (' . implode(',', $stars) . ')');
            }


            $results = $results->distinct()
                ->get();
        } else {
            // all restaurants by default
            $results = Restaurants::where("isPublic", 1)->where("active", 1)
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

        $hasSearch = '0';
        if (count($filtersBack) > 0) {
            $hasSearch = '1';
        }

        return view('frontend.search.partialSearch')->with("restaurants", $data)->with("filters", $filtersBack)->with("noLoad", true)->with("hasSearch", $hasSearch);
    }

    /**
     * Checks if user has any notifications and returns them if there are any
     * 
     * @return \Illuminate\Http\Response notification
     */
    function checkForNotification()
    {
        $notifications = Notifications::where("user_id", session()->get("user.id"))->get();

        if (!$notifications) return response("no notifications", 404);

        Notifications::where("user_id", session()->get("user.id"))->delete();

        return response()->json($notifications);
    }
}
