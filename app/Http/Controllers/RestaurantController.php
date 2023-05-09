<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\ConnectRestaurantType;
use App\Models\Menu;
use App\Models\MenuItems;
use App\Models\Orders;
use App\Models\Posts;
use App\Models\Restaurants;
use App\Models\RestaurantType;
use App\Models\Reviews;
use App\Models\Types;
use App\Models\UserRestaurant;
use App\Models\Users;
use App\Models\UsersTypes;
use Hamcrest\Arrays\IsArray;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestaurantController extends Controller
{
    function index()
    {
        // only users that aren't already with professional accounts can access this page
        if (session()->get("user.isProfessional")) return redirect("/");

        return view("frontend/create_restaurant");
    }

    /**
     * Create the restaurant
     * 
     * @param values
     */
    function create(Request $request)
    {
        if (session()->get("user.isProfessional")) response()->json(["title" => 'Erro', "message" => "Já esta associado a outro restaurante"], 500);

        if (AppHelper::hasEmpty([$request->values['name'], isset($request->values['type']) ? $request->values['type'] : []]))
            return response()->json(["title" => 'Erro', "message" => "Preencha todos os campos obrigatorios"], 200);

        if ($request->values['file'] != null && $request->values['imageUrl'] != null) {
            return response()->json(["title" => 'Erro', "message" => "Apenas pode escolher um upload de imagem"], 200);
        }

        // Save image file
        if ($request->values['file'] != null) {
            $imgName = $this->saveImage($request->values['file']);
        }

        // Create the restaurant
        $new = Restaurants::create([
            "name" => $request->values['name'],
            "description" => $request->values['description'],
            "logo_url" => $request->values["imageUrl"],
            "logo_name" => isset($imgName) ? $imgName : null,
        ]);

        if (!$new) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a criar o restaurante!"], 200);

        session(["restaurant" => [
            "id" => $new->id,
            "name" => $new->name,
            "logo_name" => $new->logo_name,
            "logo_url" => $new->logo_url,
            "isPublic" => $new->isPublic
        ]]);

        // Create menu for restaurant
        $newMenu = Menu::create([
            "label" => "Default Menu",
            "restaurant_id" => $new->id
        ]);

        if (!$newMenu) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a criar o restaurante!"], 200);

        foreach ($request->values['type'] as $type) {
            $typeid = RestaurantType::where('label', $type)->get()->first();
            if (!$typeid) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a configurar o restaurante!"], 200);
            $connT = ConnectRestaurantType::create([
                "restaurant_id" => $new->id,
                "type_id" => $typeid->id
            ]);
            if (!$connT) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a configurar o restaurante!"], 200);
        }

        // make current user a professional user
        $updatePro = Users::whereId(session()->get('user.id'))->update([
            "isProfessional" => 1
        ]);

        if (!$updatePro) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a atualizar o estado da sua conta!"], 200);

        session(["user.isProfessional" => 1]);

        // Create the user - restaurant connection
        $createConn = UserRestaurant::create([
            "user_id" => session()->get('user.id'),
            "restaurant_id" => $new->id
        ]);

        if (!$createConn) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a conectar a sua conta ao restaurante!"], 200);

        // Create the owner type and give it to the user
        $ownerType = Types::create([
            "label" => 'Owner',
            "restaurant_id" => $new->id,
            "view_orders" => 1,
            "write_orders" => 1,
            "view_menu" => 1,
            "write_menu" => 1,
            "view_stats" => 1,
            "invite_users" => 1,
            "ban_users" => 1,
            "admin" => 1,
            "owner" => 1,
            "created_by" => session()->get('user.id'),
            "created_at" => date("Y-m-d H:i:s"),
        ]);

        if (!$ownerType) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a configurar o restaurante!"], 200);

        $connectType = UsersTypes::create([
            "user_id" => session()->get('user.id'),
            "type_id" => $ownerType->id
        ]);

        if (!$connectType) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a fazer-lhe o dono do restaurante!"], 200);

        // get type to put in session
        $typeId = UsersTypes::where('user_id', session()->get('user.id'))->get()->first();
        $typeInfo = Types::whereId($typeId->type_id)->get()->first();

        session(["type" => [
            "id" => $typeInfo->id,
            "label" => $typeInfo->label,
            "view_orders" => $typeInfo->view_orders,
            "view_menu" => $typeInfo->view_menu,
            "view_stats" => $typeInfo->view_stats,
            "admin" => $typeInfo->admin,
            "owner" => $typeInfo->owner,
        ]]);

        return response()->json(["title" => "Sucesso", "message" => "Restaurante criado com sucesso!"], 200);
    }

    /**
     * gets info of the restaurant
     * 
     * @return response
     */
    function get()
    {
        // get general restaurant info
        $restaurant = Restaurants::select(
            "name",
            "description",
            "logo_name",
            "logo_url",
        )->whereId(session()->get('restaurant.id'))->get()->first();

        // get num of items in the menu
        $items = Menu::select(
            "menu_item.id",
            "menu_item.name",
        )->where('restaurant_id', session()->get('restaurant.id'))
            ->join('menu_item', 'menu.id', '=', 'menu_item.menu_id')
            ->get()
            ->count();

        // get num of permissions
        $perms = Types::where('restaurant_id', session()->get('restaurant.id'))->get()->count();

        // get num of users
        $users = UserRestaurant::where('restaurant_id', session()->get('restaurant.id'))->get()->count();

        return response()->json(["res_info" => $restaurant, "menu_count" => $items, "perms" => $perms, "users" => $users], 200);
    }

    /**
     * saves general info of the restaurant
     * 
     * @return response
     */
    function saveInfo(Request $data)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        // if it's public already you can't do this again
        if (session()->get('isPublic') == 1) return response()->json(["title" => "Erro", "message" => "Restaurante ja é publico"], 400);
        // check if name or description are empty 
        if (AppHelper::hasEmpty([$data->name, $data->description])) return response()->json(["title" => "Erro", "message" => "Preencha todos os campos"], 400);
        // check if there is a logo
        if ($data->imageUrl == null && $data->imageFile == 0) return response()->json(["title" => "Erro", "message" => "Insira um logo"], 400);
        if ($data->imageUrl != null && $data->imageFile != 0) return response()->json(["title" => "Erro", "message" => "Escolha apenas um formato de inserir imagem"], 400);

        // Save image file
        if (is_array($data->imageFile)) {
            $imgName = $this->saveImage($data->imageFile);
        } else {
            $imgName = $data->imageFile;
        }

        $save = Restaurants::whereId(session()->get('restaurant.id'))->update([
            "name" => $data->name,
            "description" => $data->description,
            "logo_url" => $data->imageUrl,
            "logo_name" => ($data->imageFile == 0 ? null : $imgName),
        ]);

        $updatedValues = Restaurants::whereId(session()->get('restaurant.id'))->get()->first();

        if (!$save) return response()->json(["title" => "Erro", "message" => "Nada para atualizar"], 400);

        session(["restaurant" => [
            "id" => $updatedValues->id,
            "name" => $updatedValues->name,
            "logo_name" => $updatedValues->logo_name,
            "logo_url" => $updatedValues->logo_url,
            "isPublic" => 0
        ]]);

        return response()->json(["title" => "Sucesso", "message" => "Informações do restaurante guardadas com sucesso"], 200);
    }

    /**
     * Publishes the restaurant
     * 
     * @return response
     */
    function publish()
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return response()->json(["title" => "Erro", "message" => "Não tem permissão para realizar esta ação"], 403);
        }

        if (session()->get('isPublic') == 1) return response()->json(["title" => "Erro", "message" => "Restaurante ja é publico"], 400);

        // get num of items in the menu
        $items = Menu::select(
            "menu_item.id",
            "menu_item.name",
        )->where('restaurant_id', session()->get('restaurant.id'))
            ->join('menu_item', 'menu.id', '=', 'menu_item.menu_id')
            ->get()
            ->count();

        if ($items < 1) return response()->json(["title" => "Erro", "message" => "Menu deve conter pelo menos 1 item"], 403);

        // get general restaurant info
        $restaurant = Restaurants::whereId(session()->get('restaurant.id'))->update([
            "isPublic" => 1
        ]);

        session()->put("restaurant.isPublic", 1);

        if (!$restaurant) return response()->json(["title" => "Erro", "message" => "Erro ao publicar o restaurante"], 500);

        return response()->json(["title" => "Sucesso", "message" => "Restaurante publicado com sucesso!"], 200);
    }

    // Save image file
    function saveImage($imageData)
    {
        $imgData = $imageData;
        $image = str_replace('data:' . $imgData['type'] . ';base64,', '', $imgData['dataURL']);
        $image = str_replace(' ', '+', $image);
        $imgName = time() . ".png";
        file_put_contents(public_path() . '/img/logos/' .  $imgName, base64_decode($image));
        return $imgName;
    }

    // Front-end methods

    /**
     * Goes to front-end restaurant page
     * 
     * @return \Illuminate\View the main page of the restaurants
     */
    function restaurant_page(Request $id)
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        $info = Restaurants::whereId($id->route('id'))->get()->first();

        //  Get the most popular items from a restaurant based on the count of how many times each item was ordered.
        $popular = Menu::select("menu_item.id", "menu_item.name", "menu_item.price", "menu_item.imageUrl")
            ->join("menu_item", "menu_item.menu_id", "menu.id")
            ->leftJoin(DB::raw("(select menu_item_ingredients.menu_item_id, menu_item_ingredients.price from menu_item_ingredients where menu_item_ingredients.default = 1) as sides"), "sides.menu_item_id", '=', 'menu_item.id')
            ->leftJoin(DB::raw("(select order_items.menu_item_id, count(*) as total from order_items group by order_items.menu_item_id) as times_ordered"), "times_ordered.menu_item_id", '=', 'menu_item.id')
            ->where("restaurant_id", $id->route('id'))
            ->groupBy("menu_item.id")
            ->orderBy("times_ordered.total", "desc")
            ->limit(10)
            ->get();

        $plateofday = Restaurants::select("restaurants.plate_of_day", "menu_item.name", "menu_item.imageUrl", "menu_item.price", "menu_item.description")
            ->join("menu_item", "menu_item.id", "=", "restaurants.plate_of_day")
            ->where("restaurants.id", $id->route('id'))
            ->get()
            ->first();

        return view("frontend.restaurants.restaurant")
            ->with("info", $info)
            ->with("popular", $popular)
            ->with("plateofday", $plateofday);
    }

    /**
     * menu page
     * 
     * @return \Illuminate\View view of the menu page
     */
    function menu_page(Request $id)
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        $info = Restaurants::whereId($id->route('id'))->get()->first();

        // Get all items, add prices of default selected side dishes to the base price
        $items_raw = Menu::select('menu_item.id', 'menu_item.name', 'menu_item.description', 'menu_item.imageUrl', DB::raw('(menu_item.price + COALESCE(SUM(menu_item_ingredients.price * menu_item_ingredients.quantity), 0)) as price'), 'menu_item.cost')
            ->join('menu_item', 'menu_item.menu_id', '=', 'menu.id')
            ->leftJoin('menu_item_ingredients', function ($join) {
                $join->on('menu_item_ingredients.menu_item_id', '=', 'menu_item.id')
                    ->where('menu_item_ingredients.default', 1);
            })
            ->where('menu.restaurant_id', $id->route('id'))
            ->groupBy('menu_item.id', 'menu_item.name', 'menu_item.description', 'menu_item.imageUrl', 'menu_item.price', 'menu_item.cost')
            ->get();


        $items = [];
        foreach ($items_raw as $item) {
            $items[] = [
                "id" => $item['id'],
                "name" => $item['name'],
                "description" => $item['description'],
                "imageUrl" => $item['imageUrl'],
                "price" => $item['price'],
                "cost" => $item['cost'],
            ];
        }

        return view("frontend.restaurants.menu")->with("info", $info)->with("items", $items);
    }

    /**
     * Goes to the reviews page
     * 
     * @return view
     */
    function reviews_page(Request $id)
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        $info = Restaurants::whereId($id->route('id'))->get()->first();

        // Get all reviews
        $reviews = Reviews::where("restaurant_id", $id->route('id'))
            ->join("users", 'users.id', '=', 'reviews.written_by')
            ->get();

        // Check if user has ordered from this restaurant, if not they will not be able to order from it.
        $canReview = Orders::where("ordered_by", session()->get("user.id"))
            ->where("restaurant_id", $info->id)
            ->get()
            ->count();

        // If user can't review then theres no need to show him his reviews since he shouldn't have any
        if ($canReview > 0) {
            $canReview = session()->get("restaurant.id") != $info->id;
            // Get all user reviews
            $myreviews = Reviews::select("reviews.title", "reviews.review", "users.pfp", "users.first_name", "users.last_name", "reviews.written_at", "reviews.stars", "reviews.id")
                ->where("restaurant_id", $id->route('id'))
                ->join("users", 'users.id', '=', 'reviews.written_by')
                ->where("users.id", session()->get("user.id"))
                ->get();
        }

        $stats = AppHelper::calculateReviewAvg($id->route('id'));

        // Decide if label should be Positivo / Maioritariamente Positivo / Indiferente / Maioritariamente Negativo / Negativo
        $label = ($stats['avg'] == 5 ? 'Positivo' : ($stats['avg'] == 4 ? 'Maioritariamente Positivo' : ($stats['avg'] == 3 ? 'Indiferente' : ($stats['avg'] == 2 ? 'Maioritariamente Negativo' : 'Negativo'))));

        return view("frontend.restaurants.reviews")->with("info", $info)
            ->with("reviews", $reviews)
            ->with("myreviews", isset($myreviews) ? $myreviews : [])
            ->with("canReview", $canReview)
            ->with("stats", $stats)
            ->with('label', $label);
    }

    /**
     * Posts page
     * 
     * @return \Illuminate\View posts
     */
    function posts(Request $id)
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        $info = Restaurants::whereId($id->route('id'))->get()->first();
        $posts = Posts::where('restaurantId', $id->route('id'))
            ->orderBy('id', 'desc')
            ->where("published", 1)
            ->get();

        return view("frontend.restaurants.posts")->with("info", $info)->with("posts", $posts);
    }
}
