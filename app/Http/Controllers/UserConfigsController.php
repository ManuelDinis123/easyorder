<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Mail\InviteMail;
use App\Models\UserRestaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserConfigsController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        return view("frontend/professional/admin/users/users");
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

        $sendToEmail = strtolower($email->email);
        Mail::to($sendToEmail)->send(new InviteMail());

        return response()->json(["title" => "Sucesso", "message" => "Convite enviado"], 200);
    }
}
