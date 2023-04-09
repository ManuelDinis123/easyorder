<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\MenuItems;
use App\Models\Posts;
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

        $posts = Posts::where("restaurantId", session()->get("restaurant.id"))
            ->where("published", 1)
            ->orderBy('id', 'desc')
            ->get();

        $drafts = Posts::where("restaurantId", session()->get("restaurant.id"))
            ->where("published", 0)
            ->orderBy('id', 'desc')
            ->get();

        $plateOfDay = Restaurants::select("restaurants.plate_of_day", "menu_item.name", "menu_item.imageUrl")
            ->join("menu_item", "menu_item.id", "=", "restaurants.plate_of_day")
            ->where("restaurants.id", session()->get("restaurant.id"))
            ->get()
            ->first();

        return view("frontend/professional/editpage/editpage")
            ->with("menuItems", $menuItemsModal)
            ->with("plateofday", $plateOfDay)
            ->with("posts", $posts)
            ->with("drafts", $drafts);
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

    /**
     * Page to create a new post
     * 
     * @return \Illuminate\View post.blade.php
     */
    function postPage(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return redirect("/professional");
        }

        $post = null;
        if ($id->get('id')) {
            $post = Posts::whereId($id->get('id'))                
                ->get()
                ->first();
        }

        return view("frontend/professional/editpage/post")->with("post", $post);
    }

    /**
     * Save a post
     * 
     * @param \Illuminate\Http\Request Post title, text of the post, isPublish
     * @return \Illuminate\Http\Response Status
     */
    function savePost(Request $data)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return response("Access Denied", 401);
        }

        if (AppHelper::hasEmpty([$data->text, $data->title])) return response()->json(["title" => "Erro", "message" => "A sua publicação esta invalida!"], 400);

        $text = htmlentities($data->text);

        if ($data->edit) {
            $res = Posts::whereId($data->edit)
                ->update([
                    "title" => $data->title,
                    "body" => $text,
                    "published" => $data->isPublish,
                    "created_by" => session()->get("user.id"),
                    "restaurantId" => session()->get("restaurant.id")
                ]);

            if (!$res) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a guardar a publicação"], 500);

            return response()->json(["title" => "Sucesso!", "message" => "Publicação publicada"], 200);
        }

        $res = Posts::create([
            "title" => $data->title,
            "body" => $text,
            "published" => $data->isPublish,
            "created_by" => session()->get("user.id"),
            "restaurantId" => session()->get("restaurant.id")
        ]);

        if (!$res) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a guardar a publicação"], 500);

        return response()->json(["title" => "Sucesso!", "message" => "Publicação publicada"], 200);
    }

    /**
     * Delete posts
     * 
     * @param \Illuminate\Http\Request id
     * @return \Illuminate\Http\Response
     */
    function deletePost(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return response("Access Denied", 401);
        }

        $res = Posts::whereId($id->id)->delete();

        if (!$res) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a remover a publicação"], 500);

        return response()->json(["title" => "Sucesso!", "message" => "Publicação removida"], 200);
    }

    /**
     * Publish a drafted post
     * 
     * @param \Illuminate\Http\Request id
     * @return \Illuminate\Http\Response
     */
    function publishDraft(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return response("Access Denied", 401);
        }

        $res = Posts::whereId($id->id)->update([
            "published"=>1
        ]);

        if (!$res) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a publicar a publicação"], 500);

        return response()->json(["title" => "Sucesso!", "message" => "Publicação publicada"], 200);
    }
}
