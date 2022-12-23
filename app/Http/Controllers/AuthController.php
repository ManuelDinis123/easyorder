<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Auth;
use Illuminate\Http\Request;
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
        
        $auth = Auth::where("user_id", $user->id)->get();

        $auth = $auth->first();
                
        if(!password_verify($request->password, $auth->password)){
            return response()->json($err, 200);
        }

        $return = ['status'=> "success", "isProfessional" => $user->isProfessional];

        return response()->json($return, 200);
    }

    // Functions to use in this controller
}
