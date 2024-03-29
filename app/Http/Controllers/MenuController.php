<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }
        if (!$this->is_item_of_restaurant($id->route('id'))) return redirect("/professional/ementa");

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }

        $restaurant_id = session()->get("restaurant")["id"];

        $tags = MenuTags::where("id_restaurant", $restaurant_id)->get();

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_menu')) return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        if (AppHelper::hasEmpty([$data->name, $data->price, $data->cost, $data->description])) return response()->json(["title" => "Erro", "message" => "Preencha todos os campos"], 400);

        $restaurant_id = session()->get("restaurant")["id"];
        $user_id = session()->get("user")["id"];

        $menu_id = Menu::where("restaurant_id", $restaurant_id)->get()->first();

        // Insert the menu_item
        $menu_item = MenuItems::create([
            "menu_id" => $menu_id->id,
            "name" => $data->name,
            "price" => $data->price,
            "cost" => $data->cost,
            "description" => $data->description,
            "imageUrl" => $data->imageurl,
            "created_by" => $user_id,
            "created_at" => date("Y-m-d H:i:s"),
        ]);

        if (!$menu_item) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 500);

        if ($data->tags) {
            $this->addTags($data->tags, $menu_item->id, true);
        }

        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " adicionou \"" . $data->name . "\" ao menu"), "/professional/ementa/" . $menu_item->id);

        return response()->json(["title" => "Sucesso", "message" => "Item adicionado com sucesso!", "id" => $menu_item->id], 200);
    }

    /**
     * Updates an item in db
     * 
     * 
     * @return Boolean
     */
    function update(Request $newdata)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_menu')) return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        if (AppHelper::hasEmpty([$newdata->name, $newdata->price, $newdata->cost, $newdata->description])) return response()->json(["title" => "Erro", "message" => "Preencha todos os campos"], 400);

        $restaurant_id = session()->get("restaurant")["id"];

        $update =  MenuItems::whereId($newdata->id)->update([
            "name" => $newdata->name,
            "price" => $newdata->price,
            "cost" => $newdata->cost,
            "description" => $newdata->description,
            "imageUrl" => $newdata->imageurl,
            "edited_by" => session()->get("user")["id"],
            "edited_at" => date("Y-m-d H:i:s")
        ]);

        if (!$update && !$newdata->tags) return response()->json(["title" => "Erro", "message" => "Erro ao editar o item"], 200);

        if ($newdata->tags || $newdata->tags_in_db) {

            // Get tags in item from DB
            $tagsinitemid = ItemTags::where("menu_item_id", $newdata->id)->get();
            $tagsinitemid = $this->obj_to_arr($tagsinitemid, "tag_id");
            $tags =  MenuTags::whereIn("id", $tagsinitemid)->get();

            // Compare tags in DB to the tags of the item to see if anything has changed.
            $tags = $this->obj_to_arr($tags, "tag");
            $tags_formdb =  $this->obj_to_arr(json_decode($newdata->tags_in_db), "value");
            $tag_diff = array_diff($tags_formdb, $tags);
            $newtags = $this->obj_to_arr(json_decode($newdata->tags), "value");

            // Merge all of the tags that need to be added
            $tags_to_add = array_merge($tag_diff, $newtags);

            $this->addTags($tags_to_add, $newdata->id, false);

            // Check if any need to be removed
            $to_remove = array_diff($tags, $tags_formdb);

            if ($to_remove) {
                $getid = MenuTags::whereIn("tag", $to_remove)
                    ->where("id_restaurant", $restaurant_id)
                    ->get()->first();

                ItemTags::where("tag_id", $getid->id)->delete();
            }
        }

        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " editou o item \"" . $newdata->name . "\" no menu"), "/professional/ementa/" . $newdata->id);

        return response()->json(["title" => "Sucesso", "message" => "Item editado com sucesso!"], 200);
    }


    /**
     * Removes an item from the db
     * 
     * @return Boolean
     */
    function remove(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_menu')) return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        // Delete the connection from the tags
        $tag_connections = ItemTags::where("menu_item_id", $id->id)->delete();

        // Delete ingredients
        $ingredients = Ingredients::where("menu_item_id", $id->id)->delete();

        $itmName = MenuItems::select("name")->where("id", $id->id)->get()->first();

        // Delete the item
        $item = MenuItems::where("id", $id->id)->delete();

        if (!$item) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao apagar o item", "erro" => $item], 500);

        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " removeu o item \"" . $itmName->name . "\" do menu"));

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_menu')) return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        if (AppHelper::hasEmpty([$data->ingredient, $data->quantity, $data->quantityType])) return response()->json(["title" => "Erro", "message" => "Preencha todos os campos"], 400);

        $ingredients = Ingredients::create([
            "ingredient" => $data->ingredient,
            "menu_item_id" => $data->id,
            "quantity" => $data->quantity,
            "price" => $data->price,
            "quantity_type" => $data->quantityType,
            "default" => $data->default,
        ]);

        if (!$ingredients) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao inserir o ingredient"], 500);

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_menu')) return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        if (AppHelper::hasEmpty([$data->ingredient, $data->quantity])) return response()->json(["title" => "Erro", "message" => "Preencha todos os campos"], 400);

        $ingredients = Ingredients::whereId($data->ingid)->update([
            "ingredient" => $data->ingredient,
            "menu_item_id" => $data->id,
            "quantity" => $data->quantity,
            "price" => $data->price,
            "quantity_type" => $data->quantityType,
            "default" => $data->default
        ]);

        if (!$ingredients) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao editar o ingredient"], 500);

        return response()->json(["title" => "Sucesso", "message" => "Ingrediente atualizado com sucesso"], 200);
    }

    /**
     * Delete an ingredient
     * 
     * @return Boolean
     */
    function delete_ingredients(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'write_menu')) return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        $delete = Ingredients::where("id", $id->id)->delete();

        if (!$delete) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro ao remover o ingrediente"], 500);

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
        if ($obj) {
            foreach ($obj as $k => $item) {
                array_push($arr, $item->$key);
            }
        }
        return $arr;
    }

    /**
     * General actions to add tags to an item
     * 
     * @return Boolean
     */
    function addTags($inputTags, $itemId, $needsConvert = true)
    {
        $restaurant_id = session()->get("restaurant")["id"];
        $connect_tags = null;

        if ($needsConvert) $inputTags = $this->obj_to_arr(json_decode($inputTags), "value");
        $dbTags = $this->obj_to_arr(MenuTags::where("id_restaurant", $restaurant_id)->get(), "tag");

        // Get the tags that aren't already in the db
        $newTags = array_diff(array_map('strtolower', $inputTags), array_map('strtolower', $dbTags)); // Put the values in lower-case so that ABC and abc are equal

        foreach ($newTags as $tag) {
            $insertTag = MenuTags::create([
                "tag" => $tag,
                "id_restaurant" => $restaurant_id
            ]);

            if (!$insertTag) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 500);
        }

        foreach ($inputTags as $tag) {
            $getid = MenuTags::where("tag", $tag)->get()->first();

            $connect_tags = ItemTags::create([
                "menu_item_id" => $itemId,
                "tag_id" => $getid->id
            ]);
        }


        if (!$connect_tags) return response()->json(["title" => "Erro", "message" => "Ocorreu um Erro"], 500);
    }

    // Check if an item is from the restaurant that the user is associated with
    function is_item_of_restaurant($item_id)
    {
        $menuid = MenuItems::whereId($item_id)->get()->first();
        $menu = Menu::whereId($menuid->menu_id)->get()->first();
        if (session()->get("restaurant.id") != $menu->restaurant_id) return 0;
        return 1;
    }
}
