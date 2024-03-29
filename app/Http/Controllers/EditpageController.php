<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Gallery;
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
        if (!AppHelper::checkAuth()) return redirect("/");
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

        // Gallery images
        $imgs = Gallery::where("restaurant_id", session()->get("restaurant.id"))->get();

        return view("frontend/professional/editpage/editpage")
            ->with("menuItems", $menuItemsModal)
            ->with("plateofday", $plateOfDay)
            ->with("posts", $posts)
            ->with("drafts", $drafts)
            ->with("imgs", $imgs);
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
        if (!AppHelper::checkAuth()) return redirect("/");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return redirect("/professional");
        }

        if(isset($id->id)){
            $platename = MenuItems::select("name")->whereId($id->id)->get()->first();
        }

        $save = Restaurants::whereId(session()->get("restaurant.id"))
            ->update([
                "plate_of_day" => isset($id->id) ? $id->id : null
            ]);

        if (!$save) return Response()->json(["title" => "Erro", "message" => "Ocorreu um erro a guardar o prato do dia"], 500);
        if(isset($id->id)){
            AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " colocou \"".$platename->name."\" como o prato do dia"), "/professional/conteudo");
        } else {
            AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " retirou o prato do dia"), "/professional/conteudo");
        }
        return Response()->json(["title" => "Sucesso", "message" => "Prato do dia adicionado com sucesso"], 200);
    }

    /**
     * Page to create a new post
     * 
     * @return \Illuminate\View post.blade.php
     */
    function postPage(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
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
        if (!AppHelper::checkAuth()) return redirect("/");
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

            AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " editou a publicação entitulada \"".$data->title."\""), "/professional/conteudo/publicar?id=" . $data->edit);

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

        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " criou uma publicação entitulada \"".$data->title."\""), "/professional/conteudo/publicar?id=" . $res->id);

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
        if (!AppHelper::checkAuth()) return redirect("/");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return response("Access Denied", 401);
        }

        $title = Posts::select("title")->whereId($id->id)->get()->first();
        $res = Posts::whereId($id->id)->delete();

        if (!$res) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a remover a publicação"], 500);
        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " apagou a publicação entitulada \"".$title->title."\""), "");
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
        if (!AppHelper::checkAuth()) return redirect("/");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return response("Access Denied", 401);
        }

        $res = Posts::whereId($id->id)->update([
            "published" => 1
        ]);

        if (!$res) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a publicar a publicação"], 500);

        return response()->json(["title" => "Sucesso!", "message" => "Publicação publicada"], 200);
    }

    /**
     * Saves gallery image
     * 
     * @param \Illuminate\Http\Request req
     * @return \Illuminate\Http\Response status
     */
    function saveImage(Request $req)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if (!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false)) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'edit_page')) return response("Access Denied", 401);
        }

        // Check if position already has any value
        $exists = Gallery::where("restaurant_id", session()->get("restaurant.id"))
            ->where("card_num", $req->pos)
            ->get()
            ->first();

        // If position is already in db just update it's imageUrl 
        if ($exists) {
            $result = Gallery::whereId($exists->id)
                ->update([
                    "imageUrl" => $req->img
                ]);
            if (!$result) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a guardar esta imagem"], 500);
            AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " adicionou uma imagem à galeria na posição " . $req->pos), "/professional/conteudo");
            return response()->json(["title" => "Sucesso!", "message" => "Imagem guardada!"], 200);
        }

        $result = Gallery::create([
            "card_num" => $req->pos,
            "restaurant_id" => session()->get("restaurant.id"),
            "imageUrl" => $req->img
        ]);

        if (!$result) return response()->json(["title" => "Erro", "message" => "Occoreu um erro a guardar esta imagem"], 500);
        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " adicionou uma imagem à galeria na posição " . $req->pos), "/professional/conteudo");
        return response()->json(["title" => "Sucesso!", "message" => "Imagem guardada!"], 200);
    }
}
