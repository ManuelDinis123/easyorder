<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\ConnectRestaurantType;
use App\Models\Restaurants;
use App\Models\RestaurantType;
use App\Models\Types;
use App\Models\UserRestaurant;
use App\Models\Users;
use App\Models\UsersTypes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateRestaurantController extends Controller
{
    function index()
    {
        // only users that aren't already with professional accounts can access this page        
        if (session()->get("user.isProfessional")  || session()->get("user.authenticated")) return redirect("/no-access");

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
            $imgData = $request->values['file'];
            $image = str_replace('data:' . $imgData['type'] . ';base64,', '', $imgData['dataURL']);
            $image = str_replace(' ', '+', $image);
            $imgName = time() . ".png";
            file_put_contents(public_path() . '/img/logos/' .  $imgName, base64_decode($image));
        }

        // Create the restaurant
        $new = Restaurants::create([
            "name" => $request->values['name'],
            "description" => $request->values['description'],
            "logo_url" => $request->values["imageUrl"],
            "logo_name" => isset($imgName) ? $imgName : null,
        ]);

        if (!$new) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a criar o restaurante!"], 200);

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

        Log::info(session()->get("user.id"));

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
            "created_by" => session()->get('user.id'),
            "created_at" => date("Y-m-d H:i:s"),
        ]);

        if (!$ownerType) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a configurar o restaurante!"], 200);

        $connectType = UsersTypes::create([
            "user_id" => session()->get('user.id'),
            "type_id" => $ownerType->id
        ]);

        if (!$connectType) return response()->json(["title" => 'Erro', "message" => "Ocorreu um erro a fazer-lhe o dono do restaurante!"], 200);

        return response()->json(["title" => "Sucesso", "message" => "Restaurante criado com sucesso!"], 200);
    }
}
