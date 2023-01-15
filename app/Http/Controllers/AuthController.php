<?php

namespace App\Http\Controllers;

use App\Models\Restaurants;
use App\Models\Users;
use App\Models\UserAuth;
use App\Models\UserRestaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{        

    /**
     * Get the Email and Password and verify them
     * 
     * @return boolean
     */
    function auth(Request $request)
    {

        $err = ['status'=> "Error", "message" => "As suas credenciais de login estão incorretas."];

        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            $err["message"] = "Insira um email valido";
            return response()->json($err, 200);
        }

        $user = Users::where("email", $request->email)->get() ?? null;                

        if (!sizeof($user)) {
            return response()->json($err, 200);
        }
        
        $user = $user->first(); // Gets the first object so that the properties can be accessed
        
        $auth = UserAuth::where("user_id", $user->id)->get();

        $auth = $auth->first();
                
        if(!password_verify($request->password, $auth->password)){
            return response()->json($err, 200);
        }

        // All data to be stored in session
        $session_data = [
            'id' => $user->id,
            'firstName'=> $user->first_name,
            'lastName' => $user->last_name,
            'birthdate' => $user->birthdate,
            'email' => $user->email,
            'isProfessional' => $user->isProfessional,
            'pfp' => $user->pfp
        ];         

        session(['authenticated' => true, "user" => $session_data]);

        if($user->isProfessional){
            $restaurantid = UserRestaurant::where("user_id", $user->id)->get()->first();
            $restaurant = Restaurants::where("id", $restaurantid->restaurant_id)->get()->first();
            session(["restaurant" => [
                "id" => $restaurant->id,
                "name" => $restaurant->name
            ]]);
        }

        $return = ['status'=> "success", "isProfessional" => $user->isProfessional];

        return response()->json($return, 200);
    }

    /**
     * Create account page
     * 
     * 
     * @return View
     */
    function createAccount() {
        return view(session()->has("authenticated") ? 'frontend/home' : 'frontend/createacc');
    }

    /**
     * Creates the account
     * 
     * @return Bool
     */
    function create(Request $user_data) {        
        // Check if email is valid
        if(!filter_var($user_data->email, FILTER_VALIDATE_EMAIL)){
            return response()->json(["title"=>"Erro", "message"=>"Email invalido!", "input"=>"email"], 200);
        }

        // Check if email already exists
        $exists = Users::where("email", $user_data->email)->get() ?? null;        

        if($exists->first()){
            return response()->json(["title"=>"Erro", "message"=>"Este email já esta em uso!", "input"=>"email"], 200);
        }

        // format date
        $birth = date("Y-m-d", strtotime($user_data->db));

        // insert into db
        $new_user = Users::create([
            "first_name" => $user_data->first,
            "last_name" => $user_data->last,
            "birthdate" => $birth,
            "email" => $user_data->email,
            "active" => 1,
            "isProfessional" => 0
        ]);

        $auth = UserAuth::create([
            "user_id" => $new_user->id,
            "password" => password_hash($user_data->password, PASSWORD_DEFAULT)
        ]);

        return response()->json(["title"=>"Success", "redirect"=>"/"], 200);

    }


    function logout() {
        session()->flush();        

        return response("/", 200);
    }
    
}
