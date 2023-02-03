<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Mail\InviteMail;
use App\Models\invite;
use App\Models\Types;
use App\Models\UserRestaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserConfigsController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        // Get user types for inviting users modal
        $types = Types::select("id", "label", "restaurant_id")->where("restaurant_id", session()->get('restaurant.id'))->get();

        $data = [];
        foreach($types as $type){
            $data[] = [
                "value"=>$type->id,
                "label"=>$type->label,
            ];
        }

        return view("frontend/professional/admin/users/users")->with(["types"=>$data]);
    }

    /**
     * Get all users from restaurant
     * 
     * @return response
     */
    function get_all()
    {
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        $users = UserRestaurant::select(
            "users.id",
            "users.first_name",
            "users.last_name",
            "users.email",
            "users.birthdate",
            "users.active",
            "users.pfp",
        )
            ->join('users', 'users.id', '=', 'user_restaurant.user_id')
            ->where('restaurant_id', session()->get('restaurant.id'))
            ->get();

        return response()->json($users, 200);
    }

    /**
     * Pending page
     * 
     * @return view
     */
    function pending_page()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        return view("frontend/professional/admin/users/pending");
    }

    /**
     * Invite users
     * 
     * @return response
     */
    function invite(Request $email)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        if (!filter_var($email->email, FILTER_VALIDATE_EMAIL)) return response()->json(["title" => "Erro", "message" => "Email invalido"], 400);
        if ($email->type==0) return response()->json(["title" => "Erro", "message" => "Deve escolher um tipo para este utilizador"], 400);

        // Create a token and associate it to the email to send in url.
        $token = Str::random(32, 'alpha_num');

        $saveInvite = invite::create([
            "token" => $token,
            "email" => $email->email,
            "restaurant_id" => session()->get('restaurant.id'),
            "type" => $email->type
        ]);

        if(!$saveInvite) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a criar o convite"], 500);

        // Send the email
        $sendToEmail = strtolower($email->email);
        Mail::to($sendToEmail)->send(new InviteMail($token));

        return response()->json(["title" => "Sucesso", "message" => "Convite enviado"], 200);
    }
}