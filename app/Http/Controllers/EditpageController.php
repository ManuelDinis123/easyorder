<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\MenuItems;
use App\Models\Restaurants;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EditpageController extends Controller
{

    /**
     * Main method
     * 
     * @return \Illuminate\View edit page view
     */
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return redirect("/professional");
        }

        $menuItems = MenuItems::select("menu_item.id", "menu_item.name", "menu_item.price", "menu_item.imageUrl")
            ->join("menu", "menu.id", "=", "menu_item.menu_id")
            ->where("menu.restaurant_id", session()->get("restaurant.id"))
            ->get();

        $menuItemsModal = [];
        foreach ($menuItems as $item) {
            $menuItemsModal[] = [
                "id" => $item['id'],
                "label" => $item['name'],
                "price" => $item['price'],
                "image" => $item['imageUrl'],
            ];
        }

        $plateOfDay = Restaurants::select("restaurants.plate_of_day", "menu_item.name", "menu_item.imageUrl")
            ->join("menu_item", "menu_item.id", "=", "restaurants.plate_of_day")
            ->where("restaurants.id", session()->get("restaurant.id"))
            ->get()
            ->first();

        return view("frontend/professional/editpage/editpage")
            ->with("menuItems", $menuItemsModal)
            ->with("plateofday", $plateOfDay);
    }

    /**
     * Gets an id of an item and sets it as plate of day for a given restaurant
     * If no id is given to the function it removes the plate of the day
     * 
     * @param \Illuminate\Http\Request id Request with the id of the menu item
     * @return \Illuminate\Http\Response status Whether the function was successful or not
     */
    function setPlateOfDay(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return redirect("/professional");
        }

        $save = Restaurants::whereId(session()->get("restaurant.id"))
            ->update([
                "plate_of_day" => isset($id->id) ? $id->id : null
            ]);

        if (!$save) return Response()->json(["title" => "Erro", "message" => "Ocorreu um erro a guardar o prato do dia"], 500);

        return Response()->json(["title" => "Sucesso", "message" => "Prato do dia adicionado com sucesso"], 200);
    }
}
