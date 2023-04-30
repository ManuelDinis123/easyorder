<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Types;
use App\Models\Users;
use App\Models\UsersTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PermsController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        return view("frontend/professional/admin/permissions/perms");
    }

    function new()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        return view("frontend/professional/admin/permissions/new");
    }

    /**
     * Opens the edit page
     * 
     * @return view
     */
    function edit_page(Request $id)
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }
        if(!$this->can_view_type($id->id)) return redirect("/professional/admin/permissions");


        $type = Types::whereId($id->id)->get()->first();

        $data = [
            "id" => $type->id,
            "label" => $type->label,
            "view_orders" => $type->view_orders,
            "write_orders" => $type->write_orders,
            "view_menu" => $type->view_menu,
            "write_menu" => $type->write_menu,
            "view_stats" => $type->view_stats,
            "invite_users" => $type->invite_users,
            "edit_page" => $type->edit_page,
            "ban_users" => $type->ban_users,
            "admin" => $type->admin,
        ];

        return view("frontend/professional/admin/permissions/edit")->with($data);
    }

    /**
     * Gets the types that exist in the db
     * 
     * @return response
     */
    function getTypes()
    {
        $perms = Types::select("id", "label")->where('restaurant_id', session()->get('restaurant.id'))->get();

        return response()->json($perms, 200);
    }

    /**
     * Saves new user types
     * 
     * @param name String
     * @param permissions array
     * @return response
     */
    function save(Request $data)
    {
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        // Initial array of values to save in db
        $newType = [
            "label" => $data->name,
            "restaurant_id" => session()->get('restaurant.id'),
            "created_by" => session()->get('user.id'),
            "created_at" => date("Y-m-d H:i:s"),
        ];

        // Adds each permission to the array
        foreach ($data->permissions as $key => $option) {
            $opt = [$key => ($option == 'true' ? 1 : 0)];
            $newType = array_merge($newType, $opt);
        }

        $save = Types::create($newType);

        if (!$save) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a guardar este tipo de user"]);
        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " criou um novo tipo de user \"".$data->name."\""), "/professional/admin/permissions/" . $save->id);
        return response()->json(["title" => "Sucesso", "message" => "Tipo de user criado com sucesso!"]);
    }

    /**
     * Edits user types
     * 
     * @param name String
     * @param permissions array
     * @return response
     */
    function edit(Request $data)
    {
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }

        // Initial array of values to save in db
        $newType = [
            "label" => $data->name,
            "restaurant_id" => session()->get('restaurant.id'),
            "edited_by" => session()->get('user.id'),
            "edited_at" => date("Y-m-d H:i:s"),
        ];

        // Adds each permission to the array
        foreach ($data->permissions as $key => $option) {
            $opt = [$key => ($option == 'true' ? 1 : 0)];
            $newType = array_merge($newType, $opt);
        }

        $edit = Types::whereId($data->id)->update($newType);

        /* Sets update_session to true to all users with this type so that if they are logged in
            while their type was updated it updates their session */
        $users_ids = UsersTypes::where('type_id', $data->id)->get();
        foreach ($users_ids as $ids) {
            Users::whereId($ids->user_id)->update([
                "update_session" => 1
            ]);
        }

        if (!$edit) return response()->json(["title" => "Erro", "message" => "Erro a atualizar o tipo de user!"], 500);
        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " editou o tipo de user \"".$data->name."\""), "/professional/admin/permissions/" . $data->id);
        return response()->json(["title" => "Sucesso", "message" => "Tipo de user atualizado com sucesso!"], 200);
    }


    /**
     * Removes type from db
     * 
     * @return response
     */
    function remove(Request $id)
    {
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/professional");
        }
        $typeName = Types::whereId($id->id)->get()->first();
        try {
            $remove = Types::whereId($id->id)->delete();
        } catch(Throwable $err){
            return response()->json(["title" => "Erro", "message" => "Não pode remover tipos que estão já estão associados a users"], 400);
        }

        if (!$remove) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a eliminar este tipo de user"], 400);
        AppHelper::recordActivity((session()->get("user.firstName") . " " . session()->get("user.lastName") . " apagou o tipo de user \"".$typeName->label."\""), "");
        return response()->json(["title" => "Sucesso", "message" => "Tipo de user eliminado com sucesso!"]);
    }

    // Check if a type is from the restaurant that the user is associated with
    function can_view_type($id)
    {
        $type = Types::whereId($id)->get()->first();
        if (session()->get("restaurant.id") != $type->restaurant_id) return 0;
        return 1;
    }
}
