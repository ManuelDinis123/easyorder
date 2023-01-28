<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Types;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        if (!AppHelper::checkUserType(session()->get("type.id"), 'owner')) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'admin')) return redirect("/professional");
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

        if (!$edit) return response()->json(["title" => "Erro", "message" => "Tipo de user atualizado com sucesso!"]);

        return response()->json(["title" => "Sucesso", "message" => "Tipo de user atualizado com sucesso!"]);
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

        $remove = Types::whereId($id->id)->delete();

        if (!$remove) return response()->json(["title" => "Erro", "message" => "Ocorreu um erro a eliminar este tipo de user"]);

        return response()->json(["title" => "Sucesso", "message" => "Tipo de user eliminado com sucesso!"]);
    }
}
