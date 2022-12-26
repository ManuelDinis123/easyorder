<?php

namespace App\Http\Controllers;

use App\Models\ItemTags;
use App\Models\Menu;
use App\Models\MenuItems;
use App\Models\MenuTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    function index()
    {
        return view("frontend/professional/menu/menu");
    }

    /**
     * Gets all the menu items from restaurant
     * 
     * 
     * @return Array
     */
    function get(Request $id)
    {
        $menu = Menu::where("restaurant_id", $id->id)->get(); // Get the menu that is associated with the id of the restaurant

        $menu = $menu->first();

        $menu_items = MenuItems::where("menu_id", $menu->id)->get() ?? null; // Get the items from that menu, if there are none return null

        $all_items = [];
        foreach ($menu_items as $item) {
            // Push an Array with all the necessary data into the all items array
            array_push($all_items, [
                "title" => $item->name . " <br /> <span class='text-muted'>" . $item->description . "</span>",
                "price" => $item->price . "€",
                "actions" => '
                <span>
                <i class="fa-sharp fa-solid fa-pen" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i>
                <i class="fa-sharp fa-solid fa-trash-xmark" style="color:#bf1313; cursor:pointer;"></i>
                </span>
                '
            ]);
        }

        Log::info($all_items);

        return response()->json($all_items, 200);
    }

    /**
     * Function to create a new Food Item
     * 
     * 
     * @return Boolean
     */
    function create(Request $data)
    {

        // Insert the menu_item
        $menu_item = MenuItems::create([
            "menu_id" => 1, // ! PLACEHOLDER VALUE
            "name" => $data->name,
            "price" => $data->price,
            "cost" => $data->cost,
            "description" => $data->description,
            "created_by" => 1,
            "created_at" => date("Y-m-d"),
        ]);

        if (!$menu_item) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 200);

        $alltags = $this->obj_to_arr(json_decode($data->tags), "value");
        $dbTags = $this->obj_to_arr(MenuTags::where("id_restaurant", 1)->get(), "tag");

        // Get the tags that aren't already in the db
        $newTags = array_diff($alltags, $dbTags);

        foreach ($newTags as $tag) {
            $insertTag = MenuTags::create([
                "tag" => $tag,
                "id_restaurant" => 1 // ! PLACEHOLDER VALUE
            ]);

            if (!$insertTag) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 200);

            $connect_tags = ItemTags::create([
                "menu_item_id" => $menu_item->id,
                "tag_id" => $insertTag->id
            ]);

            if (!$connect_tags) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 200);
        }

        return response()->json(["title" => "Sucesso", "message" => "Item adicionado com sucesso!"], 200);
    }


    /**
     * Populate an Array with values from an item in an Object
     *      
     * 
     * @return array
     */
    function obj_to_arr($obj, $key)
    {
        $arr = [];
        foreach ($obj as $k => $item) {
            array_push($arr, $item->$key);
        }
        return $arr;
    }
}
