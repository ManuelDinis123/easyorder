<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\ConnectRestaurantType;
use App\Models\Menu;
use App\Models\MenuItems;
use App\Models\Restaurants;
use App\Models\RestaurantType;
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

        return response()->json(["res_info" => $restaurant, "menu_count" => $items], 200);
    }

    /**
     * saves general info of the restaurant
     * 
     * @return response
     */
    function saveInfo(Request $data)
    {
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
}
