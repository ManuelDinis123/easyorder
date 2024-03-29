<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\CartItems;
use App\Mail\forgotpswMail;
use App\Models\Forgotpassword;
use App\Models\invite;
use App\Models\Restaurants;
use App\Models\Shoppingcart;
use App\Models\SideDishes;
use App\Models\Types;
use App\Models\Users;
use App\Models\UserAuth;
use App\Models\UserRestaurant;
use App\Models\UsersTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    function index()
    {
        return view("frontend.login");
    }


    /**
     * Get the Email and Password and verify them
     * 
     * @return boolean
     */
    function auth(Request $request)
    {

        $err = ['status' => "Error", "message" => "As suas credenciais de login estão incorretas."];

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $err["message"] = "Insira um email valido";
            return response()->json($err, 400);
        }

        $user = Users::where("email", $request->email)->get() ?? null;

        if (!sizeof($user)) {
            return response()->json($err, 400);
        }

        $user = $user->first(); // Gets the first object so that the properties can be accessed

        $auth = UserAuth::where("user_id", $user->id)->get();

        $auth = $auth->first();

        if (!password_verify($request->password, $auth->password)) {
            return response()->json($err, 400);
        }

        if ($user->banned) {
            return response()->json(["status" => "Atenção", "message" => "Foi banido da app por tempo indefinido"], 400);
        }

        // All data to be stored in session
        $session_data = [
            'id' => $user->id,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'birthdate' => $user->birthdate,
            'email' => $user->email,
            'isProfessional' => $user->isProfessional,
            'active' => $user->active,
            'pfp' => $user->pfp
        ];

        session(['authenticated' => true, "user" => $session_data]);

        if ($user->isProfessional) {
            $restaurantid = UserRestaurant::where("user_id", $user->id)->get()->first();
            $restaurant = Restaurants::where("id", $restaurantid->restaurant_id)->get()->first();
            session(["restaurant" => [
                "id" => $restaurant->id,
                "name" => $restaurant->name,
                "logo_name" => $restaurant->logo_name,
                "logo_url" => $restaurant->logo_url,
                "isPublic" => $restaurant->isPublic,
                "isActive" => $restaurant->active
            ]]);

            // get type to put in session
            $typeId = UsersTypes::where('user_id', $user->id)->get()->first();
            $typeInfo = Types::whereId($typeId->type_id)->get()->first();

            session(["type" => [
                "id" => $typeInfo->id,
                "label" => $typeInfo->label,
                "view_orders" => $typeInfo->view_orders,
                "view_menu" => $typeInfo->view_menu,
                "view_stats" => $typeInfo->view_stats,
                "write_orders" => $typeInfo->write_orders,
                "write_menu" => $typeInfo->write_menu,
                "invite_users" => $typeInfo->invite_users,
                "edit_page" => $typeInfo->edit_page,
                "ban_users" => $typeInfo->ban_users,
                "admin" => $typeInfo->admin,
                "owner" => $typeInfo->owner,
            ]]);
        }

        // Sets logged_in to 
        $logged = Users::whereId(session()->get('user.id'))->update([
            "logged_in" => 1
        ]);

        $return = ['status' => "success", "isProfessional" => ($user->isProfessional && $user->active)];

        return response()->json($return, 200);
    }

    /**
     * Create account page
     * 
     * 
     * @return View
     */
    function createAccount()
    {
        return view(session()->has("authenticated") ? 'frontend/home' : 'frontend/createacc');
    }

    /**
     * Creates the account
     * 
     * @return Bool
     */
    function create(Request $user_data)
    {
        // Check if email is valid
        if (!filter_var($user_data->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(["title" => "Erro", "message" => "Email invalido!", "input" => "email"], 200);
        }

        // Check if email already exists
        $exists = Users::where("email", $user_data->email)->get() ?? null;

        if ($exists->first()) {
            return response()->json(["title" => "Erro", "message" => "Este email já esta em uso!", "input" => "email"], 200);
        }

        // format date
        $birth = $user_data->db;

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

        return response()->json(["title" => "Success", "redirect" => "/"], 200);
    }

    /**
     * Logs out the user
     * 
     */
    function logout()
    {
        AppHelper::logout();

        return response("/", 200);
    }

    /**
     * Updates user session permissions if update_session is set to true
     * 
     * @return response
     */
    function update_session()
    {
        $hasToUpdate = Users::select("update_session")->whereId(session()->get('user.id'))->get()->first();
        if ($hasToUpdate->update_session) {
            // Get new permissions
            $type_id = UsersTypes::where('user_id', session()->get('user.id'))->get()->first();
            $type_permissions = Types::select(
                "id",
                "label",
                "view_orders",
                "view_menu",
                "view_stats",
                "edit_page",
                "ban_users",
                "invite_users",
                "admin",
                "owner",
            )->whereId($type_id->type_id)->get()->first();
            session(["type" => [
                "id" => $type_permissions->id,
                "label" => $type_permissions->label,
                "view_orders" => $type_permissions->view_orders,
                "view_menu" => $type_permissions->view_menu,
                "view_stats" => $type_permissions->view_stats,
                "write_orders" => $type_permissions->write_orders,
                "write_menu" => $type_permissions->write_menu,
                "invite_users" => $type_permissions->invite_users,
                "edit_page" => $type_permissions->edit_page,
                "ban_users" => $type_permissions->ban_users,
                "admin" => $type_permissions->admin,
                "owner" => $type_permissions->owner,
            ]]);
            // Set update_session back to 0
            Users::whereId(session()->get('user.id'))->update([
                "update_session" => 0
            ]);

            return response()->json(["title" => "Atenção!", "message" => "As suas permissões foram alteradas. Faça login novamente para ter todas as mudanças.", "newSession" => [
                "view_menu" => session()->get("type.view_menu"),
                "write_menu" => session()->get("type.write_menu"),
                "write_orders" => session()->get("type.write_orders"),
                "view_orders" => session()->get("type.view_orders"),
                "view_stats" => session()->get("type.view_stats"),
                "admin" => session()->get("type.admin"),
                "owner" => session()->get("type.owner"),
                "invite_users" => session()->get("type.invite_users"),
                "ban_users" => session()->get("type.ban_users"),
                "edit_page" => session()->get("type.edit_page"),
            ]], 200);
        } else {
            return response(0, 200);
        }
    }

    /**
     * Page to redirect after user accepted invite
     * 
     * @return view
     */
    function invited(Request $token)
    {
        $fullData = [];

        // Get info
        $info = invite::where('token', $token->route('token'))
            ->get()
            ->first();

        if (!$info) return redirect("/");

        // Get restaurant info
        $restaurant = Restaurants::select(
            "id",
            "name",
            "logo_url",
            "logo_name",
        )->whereId($info->restaurant_id)
            ->get()
            ->first();

        $fullData = array_merge($fullData, [
            "email" => $info->email,
            "token" => $info->token,
            "type" => $info->type,
            "r_id" => $restaurant->id,
            "r_name" => $restaurant->name,
            "logo_url" => $restaurant->logo_url,
            "logo_name" => $restaurant->logo_name,
        ]);

        // Check if this email already has an account
        $hasAccount = Users::where('email', $info->email)->get()->first();

        if ($hasAccount) {
            $fullData = array_merge($fullData, [
                "userID" => $hasAccount->id,
                "username" => $hasAccount->first_name . ' ' . $hasAccount->last_name,
                "pfp" => $hasAccount->pfp
            ]);

            // Check if account is associated with any other restaurant
            $hasRestaurant = UserRestaurant::where('user_id', $hasAccount->id)->get()->first();

            if ($hasRestaurant) {
                $fullData = array_merge($fullData, [
                    "hasRestaurant" => true,
                ]);

                return view("frontend.invited.accepted")
                    ->with($fullData);
            }
        }        
        return view("frontend.invited.accepted")
            ->with($fullData);
    }

    /**
     * Actions to take when user accepts invite
     * 
     * @return response
     */
    function invite_finish(Request $data)
    {
        if ($data->is_create) {
            // Check if email is valid
            if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(["title" => "Erro", "message" => "Email invalido!"], 400);
            }

            // Check if email already exists
            $exists = Users::where("email", $data->email)->get() ?? null;

            if ($exists->first()) {
                return response()->json(["title" => "Erro", "message" => "Este email já esta em uso!"], 400);
            }

            // format date
            $birth = date("Y-m-d", strtotime($data->db));

            // insert into db
            $new_user = Users::create([
                "first_name" => $data->first,
                "last_name" => $data->last,
                "birthdate" => $birth,
                "email" => $data->email,
                "active" => 1,
                "isProfessional" => 1
            ]);

            if (!$new_user) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a criar a sua conta"], 500);

            $auth = UserAuth::create([
                "user_id" => $new_user->id,
                "password" => password_hash($data->password, PASSWORD_DEFAULT)
            ]);

            if (!$auth) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a criar a sua conta"], 500);

            $this->switchToPro($new_user->id, $data->restaurant_id, $data->type);
        } elseif ($data->has_account) {
            // Check if password is correct
            $pssw = UserAuth::where("user_id", $data->userID)->get()->first();

            if (!password_verify($data->password, $pssw->password)) return response()->json(["title" => "Erro", "message" => "Password errada"], 403);

            $turnPro = Users::whereId($data->userID)->update([
                "isProfessional" => 1
            ]);

            if (!$turnPro) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a mudar a sua conta para professional"], 500);

            $this->switchToPro($data->userID, $data->restaurant_id, $data->type);
        } else {
            return response()->json(["title" => "Erro", "message" => "Ocorreu um erro"], 500);
        }

        // Delete the invite
        invite::where('token', $data->inv_uid)->delete();
        return response()->json(["title" => "Sucesso", "message" => "Conta criada com sucesso"], 200);
    }

    /**
     * Turns a user account to a pro account and associates them to a restaurant
     * 
     * @return Response
     */
    function switchToPro($userID, $restaurantID, $typeID)
    {
        $connect_user_restaurant = UserRestaurant::create([
            "user_id" => $userID,
            "restaurant_id" => $restaurantID
        ]);

        if (!$connect_user_restaurant) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a connectar a sua conta ao restaurante"], 500);

        $userType = UsersTypes::create([
            "user_id" => $userID,
            "type_id" => $typeID
        ]);

        $restaurantid = UserRestaurant::where("user_id", $userID,)->get()->first();
        $restaurant = Restaurants::where("id", $restaurantid->restaurant_id)->get()->first();
        session(["restaurant" => [
            "id" => $restaurant->id,
            "name" => $restaurant->name,
            "logo_name" => $restaurant->logo_name,
            "logo_url" => $restaurant->logo_url,
            "isPublic" => $restaurant->isPublic,
            "isActive" => $restaurant->active
        ]]);

        // get type to put in session
        $typeId = UsersTypes::where('user_id', $userID,)->get()->first();
        $typeInfo = Types::whereId($typeId->type_id)->get()->first();

        session(["type" => [
            "id" => $typeInfo->id,
            "label" => $typeInfo->label,
            "view_orders" => $typeInfo->view_orders,
            "view_menu" => $typeInfo->view_menu,
            "view_stats" => $typeInfo->view_stats,
            "write_orders" => $typeInfo->write_orders,
            "write_menu" => $typeInfo->write_menu,
            "invite_users" => $typeInfo->invite_users,
            "edit_page" => $typeInfo->edit_page,
            "ban_users" => $typeInfo->ban_users,
            "admin" => $typeInfo->admin,
            "owner" => $typeInfo->owner,
        ]]);

        if (!$userType) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a connectar a sua conta ao restaurante"], 500);
    }

    /**
     * send forgot password email
     * 
     * @param \Illuminate\Http\Request email
     * @return \Illuminate\Http\Response status
     */
    function forgot(Request $req)
    {
        if(!$req->email) return response()->json(["title"=>"Erro", "message"=>"Insira um email"], 400);

        $token = Str::random(32, 'alpha_num');

        $email = Forgotpassword::create([
            "token" => $token,
            "email"=>$req->email
        ]);

        if(!$email) return response()->json(["title"=>"Erro", "message"=>"Ocorreu um erro a mandar o email"], 500);

        $sendToEmail = strtolower($req->email);
        Mail::to($sendToEmail)->send(new forgotpswMail($token));
        return response()->json(["title" => "Sucesso", "message" => "Email para repor password enviado"], 200);
    }

    /**
     * Forgot password page
     * 
     * @return View
     */
    function forgotPass(Request $token)
    {
        $fullData = [];

        // Get info
        $info = Forgotpassword::where('token', $token->route('token'))
            ->get()
            ->first();

        $emailExists = Users::where("email", $info->email)->get();
        if(count($emailExists)<=0){
            return redirect("/");
        }

        if (!$info) return redirect("/");

        return view("frontend.forgot")->with("email", $info->email)->with("token", $token->route('token'));
    }

    /**
     * Save new password
     * 
     * @param \Illuminate\Http\Request password
     * @return \Illuminate\Http\Response status
     */
    function saveNewPassword(Request $req)
    {
        if(!$req->password){
            return response()->json(["title" => "Erro", "message" => "insira uma password!"], 500);
        }

        $info = Forgotpassword::where('token', $req->token)
        ->get()
        ->first();

        $psw = password_hash($req->password, PASSWORD_DEFAULT);

        $id = Users::where('email', $info->email)->get()->first();
        $save = UserAuth::where("user_id", $id->id)->update([
            "password" => $psw
        ]);

        if(!$save){
            return response()->json(["title" => "Erro", "message" => "ocorreu um erro a mudar a sua password!"], 500);
        }

        Forgotpassword::where('token', $req->token)->delete();

        return response()->json(["title" => "Sucesso", "message" => "sucesso!"]);
    }
}
