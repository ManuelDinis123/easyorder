<?php

namespace App\Http\Controllers;

use App\Models\Ingredients;
use App\Models\ItemTags;
use App\Models\Menu;
use App\Models\MenuItems;
use App\Models\MenuTags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    /**
     * Index method of the MenuController
     * 
     * @return View
     */
    function index()
    {
        return view("frontend/professional/menu/menu");
    }

    /**
     * Gets the info of the menu item and sends them to the view to be loaded
     * 
     * 
     * @return View
     */
    function edit(Request $id)
    {
        // Get data from the menu item
        $item = MenuItems::where("id", $id->route('id'))->get()->first();

        // Get the id of the tags that are associated with this item
        $tags_ids = ItemTags::where("menu_item_id", $id->route('id'))->get();
        $tags_ids = $this->obj_to_arr($tags_ids, "tag_id");

        // Get tags from the ids array
        $tags = $this->obj_to_arr(MenuTags::whereIn("id", $tags_ids)->get(), "tag");

        // pass to valid array
        $item = [
            "id" => $item->id,
            "name" => $item->name,
            "price" => $item->price,
            "cost" => $item->cost,
            "description" => $item->description,
            "imageurl" => $item->imageUrl,
            "tags" => json_encode($tags)
        ];

        return view("frontend/professional/menu/edit")->with($item);
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
                "id" => $item->id,
                "title" => $item->name . " <br /> <span class='text-muted'>" . $item->description . "</span>",
                "price" => $item->price . "€",
            ]);
        }

        return response()->json($all_items, 200);
    }

    /**
     * get all tags from the restaurant
     * 
     * @return Array
     */
    function get_tags()
    {
        $tags = MenuTags::where("id_restaurant", 1)->get(); // ! 1 is a placeholder value        

        return response()->json($tags, 200);
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
            "imageUrl" => $data->imageurl,
            "created_by" => 1,
            "created_at" => date("Y-m-d"),
        ]);

        if (!$menu_item) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 200);

        if ($data->tags) {
            $this->addTags($data->tags, $menu_item->id);
        }


        return response()->json(["title" => "Sucesso", "message" => "Item adicionado com sucesso!"], 200);
    }

    /**
     * Updates an item in db
     * 
     * 
     * @return Boolean
     */
    function update(Request $newdata)
    {
        $update =  MenuItems::whereId($newdata->id)->update([
            "name" => $newdata->name,
            "price" => $newdata->price,
            "cost" => $newdata->cost,
            "description" => $newdata->description,
            "imageUrl" => $newdata->imageurl
        ]);

        if (!$update && !$newdata->tags) return response()->json(["title" => "Erro", "message" => "Erro ao editar o item"], 200);

        if ($newdata->tags) {
            $this->addTags($newdata->tags, $newdata->id);
        }

        return response()->json(["title" => "Sucesso", "message" => "Item editado com sucesso!"], 200);
    }


    /**
     * Removes an item from the db
     * 
     * @return Boolean
     */
    function remove(Request $id)
    {
        // Delete the connection from the tags
        $tag_connections = ItemTags::where("menu_item_id", $id->id)->delete();

        // Delete ingredients
        $ingredients = Ingredients::where("menu_item_id", $id->id)->delete();

        // Delete the item
        $item = MenuItems::where("id", $id->id)->delete();

        if (!$item) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao apagar o item", "erro" => $item], 200);

        return response()->json(["title" => "Sucesso", "message" => "Item apagado com sucesso"], 200);
    }

    /**
     * Get ingredients from an item
     * 
     * 
     * @return Array
     */
    function fetch_ingredients(Request $id)
    {

        $ingredients = Ingredients::where("menu_item_id", $id->id)->get() ?? null;

        return response()->json($ingredients, 200);
    }

    /**
     * Adds ingredients
     * 
     * 
     * @return Boolean
     */
    function add_ingredients(Request $data)
    {   
        $ingredients = Ingredients::create([
            "ingredient" => $data->ingredient,
            "menu_item_id" => $data->id,
            "quantity" => $data->quantity
        ]);

        if(!$ingredients) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao inserir o ingredient"], 200);

        return response()->json(["title" => "Sucesso", "message" => "Ingrediente inserido com sucesso"], 200);
    }

    
    /**
     * Update ingredients
     * 
     * 
     * @return Boolean
     */
    function update_ingredients(Request $data)
    {   
        $ingredients = Ingredients::whereId($data->ingid)->update([
            "ingredient" => $data->ingredient,
            "menu_item_id" => $data->id,
            "quantity" => $data->quantity
        ]);

        if(!$ingredients) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao editar o ingredient"], 200);

        return response()->json(["title" => "Sucesso", "message" => "Ingrediente atualizado com sucesso"], 200);
    }

    /**
     * Delete an ingredient
     * 
     * @return Boolean
     */
    function delete_ingredients(Request $id)
    {
        $delete = Ingredients::where("id", $id->id)->delete();

        if (!$delete) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao remover o ingrediente"], 200);

        return response()->json(["title" => "Sucesso", "message" => "Ingrediente removido com sucesso"], 200);
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

    /**
     * General actions to add tags to an item
     * 
     * @return Boolean
     */
    function addTags($inputTags, $itemId)
    {
        $alltags = $this->obj_to_arr(json_decode($inputTags), "value");
        $dbTags = $this->obj_to_arr(MenuTags::where("id_restaurant", 1)->get(), "tag"); // ! 1 is a PLACEHOLDER VALUE

        // Get the tags that aren't already in the db
        $newTags = array_diff($alltags, $dbTags);

        foreach ($newTags as $tag) {
            $insertTag = MenuTags::create([
                "tag" => $tag,
                "id_restaurant" => 1 // ! PLACEHOLDER VALUE
            ]);

            if (!$insertTag) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 200);
        }

        foreach ($alltags as $tag) {
            $getid = MenuTags::where("tag", $tag)->get()->first();

            $connect_tags = ItemTags::create([
                "menu_item_id" => $itemId,
                "tag_id" => $getid->id
            ]);
        }


        if (!$connect_tags) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 200);
    }
}
