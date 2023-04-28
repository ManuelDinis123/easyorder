<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Gallery;
use App\Models\Menu;
use App\Models\OrderItems;
use App\Models\Restaurants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    function index()
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        $today = now()->format('Y-m-d');

        // Get most orders on the current day
        $mostOrders = OrderItems::select("menu_item.id", DB::raw("restaurants.id as restaurantID"), DB::raw("restaurants.name as restaurant_name"), DB::raw("count(order_items.id) as orderCount"), "menu_item.name", "menu_item.imageUrl", "menu_item.price")
            ->join("orders", "orders.id", "=", "order_items.order_id")
            ->join("menu_item", "menu_item.id", "=", "order_items.menu_item_id")
            ->join("menu", "menu.id", "=", "menu_item.menu_id")
            ->join("restaurants", "restaurants.id", "=", "menu.restaurant_id")
            ->where("orders.deadline", "LIKE", $today . "%")
            ->groupBy("order_items.menu_item_id")
            ->orderBy("orderCount", "desc")
            ->limit(10)
            ->get();

        // Get best rating
        $bestRating = Restaurants::select("restaurants.id", "restaurants.name", "restaurants.description", DB::raw("ROUND(SUM(reviews.stars)/COUNT(reviews.id)) as average"))
            ->join("reviews", "reviews.restaurant_id", "=", "restaurants.id")
            ->where("restaurants.isPublic", 1)
            ->where("restaurants.active", 1)
            ->groupBy("restaurants.id")
            ->orderBy("average", "desc")
            ->limit(5)
            ->get();

        $max = (count($bestRating) - 1);
        $showcase = $bestRating[rand(0, $max)];

        $menuImgs = Menu::select("menu_item.imageUrl")
            ->join("menu_item", "menu_item.menu_id", "=", "menu.id")
            ->where("restaurant_id", $showcase->id)
            ->get();

        $gallery = Gallery::where("restaurant_id", $showcase->id)->orderBy("gallery.card_num")->get();

        return view('frontend/home')
            ->with("orders", $mostOrders)
            ->with("showcase", $showcase)
            ->with("gallery", $gallery)
            ->with("menuImgs", $menuImgs);
    }
}
