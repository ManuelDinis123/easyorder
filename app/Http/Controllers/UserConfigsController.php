<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Mail\InviteMail;
use App\Models\invite;
use App\Models\Types;
use App\Models\UserRestaurant;
use App\Models\Users;
use App\Models\UsersTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserConfigsController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'invite_users'], false))) {
            return redirect("/professional");
        }

        // Get user types for inviting users modal
        $types = Types::select("id", "label", "restaurant_id")->where("restaurant_id", session()->get('restaurant.id'))->get();

        $data = [];
        foreach ($types as $type) {
            if ($type->label != 'Owner') {
                $data[] = [
                    "value" => $type->id,
                    "label" => $type->label,
                ];
            }
        }

        return view("frontend/professional/admin/users/users")->with(["types" => $data]);
    }

    /**
     * Get all users from restaurant
     * 
     * @return response
     */
    function get_all()
    {
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'invite_users'], false))) {
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
            "types.owner",
            "types.admin"
        )
            ->join('users', 'users.id', '=', 'user_restaurant.user_id')
            ->join('users_types', 'users_types.user_id', '=', 'users.id')
            ->join('types', 'types.id', '=', 'users_types.type_id')
            ->where('user_restaurant.restaurant_id', session()->get('restaurant.id'))
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
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'invite_users'], false))) {
            return redirect("/professional");
        }

        return view("frontend/professional/admin/users/pending");
    }

    /**
     *  Get pending invites
     * 
     * @return view
     */
    function getPending()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'invite_users'], false))) {
            return redirect("/professional");
        }

        // Get all invites
        $all_pending_invites = invite::select("invite.token", "invite.email", "types.label")
            ->join('types', 'types.id', '=', 'invite.type')
            ->where('invite.restaurant_id', session()->get('restaurant.id'))
            ->get();


        $pending = [];
        foreach ($all_pending_invites as $invite) {
            $pending[] = [
                "token" => $invite->token,
                "email" => $invite->email,
                "label" => $invite->label,
            ];
        }

        return response()->json($pending, 200);
    }

    /**
     * Invite users
     * 
     * @return response
     */
    function invite(Request $email)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'invite_users'], false))) {
            return redirect("/professional");
        }

        if (!filter_var($email->email, FILTER_VALIDATE_EMAIL)) return response()->json(["title" => "Erro", "message" => "Email invalido"], 400);
        if ($email->type == 0) return response()->json(["title" => "Erro", "message" => "Deve escolher um tipo para este utilizador"], 400);

        // Check if already sent an invite to that email
        $invitedAlready = invite::where('email', $email->email)->get();

        if (count($invitedAlready) > 0) return response()->json(["title" => "Erro", "message" => "Já mando um convite para esse email"], 400);

        // Check if that email is already in the restaurant
        $inRestaurant = Users::select('email')
            ->join('user_restaurant', 'user_restaurant.user_id', '=', 'users.id')
            ->where([
                ['user_restaurant.restaurant_id', '=', session()->get('restaurant.id')],
                ['users.email', '=', $email->email],
            ])->get()->first();

        if (isset($inRestaurant->email)) return response()->json(["title" => "Erro", "message" => "Este utilizador já esta no restaurante"], 400);

        // Create a token and associate it to the email to send in url.
        $token = Str::random(32, 'alpha_num');

        $saveInvite = invite::create([
            "token" => $token,
            "email" => $email->email,
            "restaurant_id" => session()->get('restaurant.id'),
            "type" => $email->type
        ]);

        if (!$saveInvite) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a criar o convite"], 500);

        // Send the email
        $sendToEmail = strtolower($email->email);
        Mail::to($sendToEmail)->send(new InviteMail($token));
        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " mandou um convite para o email \"".$email->email."\""), "/professional/admin/users/pending");
        return response()->json(["title" => "Sucesso", "message" => "Convite enviado"], 200);
    }

    /**
     * To remove invites
     * 
     * @return response
     */
    function delete_invites(Request $data)
    {
        $del = invite::where('token', $data->id)->delete();
        if (!$del) response()->json(["title" => "Erro", "message" => "Erro a remover o convite"], 500);

        return response()->json(["title" => "Sucesso", "message" => "Removido com sucesso"], 200);
    }

    /**
     *  User details page
     * 
     * @return view
     */
    function user_details(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }
        if (!$this->is_user_of_restaurant($id->route('id'))) return redirect("/professional/admin/users");

        $user = Users::join('users_types', 'users_types.user_id', '=', 'users.id')
            ->join('types', 'types.id', '=', 'users_types.type_id')
            ->where('users.id', $id->route('id'))->get()->first();

        // for type dropdown
        $allTypes = Types::where('restaurant_id', session()->get('restaurant.id'))->get();

        $typesforview = [];
        foreach ($allTypes as $type) {
            $typesforview[] = [
                "id" => $type->id,
                "label" => $type->label,
                "owner" => $type->owner
            ];
        }

        return view("frontend/professional/admin/users/details")->with([
            "id" => $id->route('id'),
            "name" => $user->first_name . " " . $user->last_name,
            "email" => $user->email,
            "birthdate" => $user->birthdate,
            "active" => $user->active,
            "pfp" => $user->pfp,
            "label" => $user->label,
            "typeID" => $user->type_id,
            "permissions_description" => [
                "view_orders" => $user->view_orders ? 'Ver pedidos' : '',
                "write_orders" => $user->write_orders ? 'Fazer alterações nos pedidos' : '',
                "view_menu" => $user->view_menu ? 'Ver a ementa' : '',
                "write_menu" => $user->write_menu ? 'Fazer alterações na Ementa e os seus respetivos items' : '',
                "view_stats" => $user->view_stats ? 'Ver as estatísticas do restaurante' : '',
                "invite_users" => $user->invite_users ? 'Convidar outros utilizadores' : '',
                "ban_users" => $user->ban_users ? 'Banir utilizadores' : '',
            ],
            "isOwner" => $user->owner,
            "isAdmin" => $user->admin,
        ])
            ->with("types", $typesforview);
    }

    /**
     * Change type of a user
     * 
     * @return response
     */
    function changeType(Request $ids)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        // User musn't be an owner and admin users can't change other admin users
        $current_type = UsersTypes::join('types', 'types.id', '=', 'type_id')
            ->where('user_id', $ids->user_id)->get()->first();

        if ($current_type->owner || ($current_type->admin && !session()->get("type.owner"))) return response()->json(["title" => "Erro", "message" => "Não pode mudar o tipo deste utilizador"], 403);

        // Change user type
        $update = UsersTypes::where('user_id', $ids->user_id)->update([
            "type_id" => $ids->new_type
        ]);

        if (!$update) return response()->json(["title" => "Erro", "message" => "Erro a atualizar o tipo de user"], 500);

        // Set update session to 1 for that user
        Users::whereId($ids->user_id)->update([
            "update_session" => 1
        ]);

        return response()->json(["title" => "Sucesso", "message" => "Tipo mudado com sucesso"], 200);
    }

    /**
     * Actives / Deactivates users
     * 
     * @return response
     */
    function changeUserState(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'ban_users'], false))) {
            return response()->json(["title" => "Erro", "message" => "Não tem permissões para " . ($id->active == 1 ? 'ativar' : 'desativar') . " o User"], 403);
        }

        $name = Users::select(DB::raw("Concat(first_name, ' ', last_name) as name"))->whereId($id->id)->get()->first();

        $state = Users::whereId($id->id)->update([
            "active" => $id->active
        ]);

        if (!$state) return response()->json(["title" => "Erro", "message" => "Erro a " . ($id->active == 1 ? 'ativar' : 'desativar') . " este user"], 500);
        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " " . ($id->active == 1 ? 'ativou' : 'desativou') . " o/a user " . $name->name), "");
        return response()->json(["title" => "Sucesso", "message" => "User " . ($id->active == 1 ? 'ativado' : 'desativado') . " com sucesso"], 200);
    }

    // Check if an user is from the restaurant that the user is associated with
    function is_user_of_restaurant($id)
    {
        $userID = UserRestaurant::where('user_id', $id)->get()->first();
        if (session()->get("restaurant.id") != $userID->restaurant_id) return 0;
        return 1;
    }
}
