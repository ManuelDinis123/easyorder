<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Activity;
use App\Models\Restaurants;
use App\Models\RestaurantType;
use App\Models\Types;
use App\Models\UserAuth;
use App\Models\UserRestaurant;
use App\Models\Users;
use App\Models\UsersTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    function index()
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        return view("frontend/professional/settings/user");
    }

    function admin()
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            return redirect("/");
        }

        $users = Users::select("users.id", "users.first_name", "users.last_name")
            ->join("user_restaurant", "user_restaurant.user_id", "=", "users.id")
            ->where("user_restaurant.restaurant_id", session()->get("restaurant.id"))
            ->where("users.id", "!=", session()->get("user.id"))
            ->get();

        $activities = Activity::where("restaurant_id", session()->get("restaurant.id"))
            ->join("users", "users.id", "=", "activity.user_id")
            ->orderBy("created_at", "desc")->get();

        return view("frontend/professional/settings/admin")
            ->with("users", $users)
            ->with("activities", $activities);
    }

    function general()
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'edit_page'], false))) {
            return redirect("/");
        }

        $info = Restaurants::whereId(session()->get("restaurant.id"))->get()->first();

        return view("frontend/professional/settings/general")->with("info", $info);
    }

    /**
     * Save changes made in general tab
     * 
     * @param \Illuminate\Http\Request info
     * @return \Illuminate\Http\Response status
     */
    function saveGeneralChanges(Request $info)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin', 'edit_page'], false))) {
            return redirect("/");
        }

        if(AppHelper::hasEmpty([$info->name, $info->description])){
            return response()->json(["title" => "Erro", "message" => "Preencha todos os campos!"], 400);
        }

        $newData = [
            "name" => $info->name, 
            "description" => $info->description
        ];
        if($info->logo_url != null){
            $newData['logo_url'] = $info->logo_url['dataURL'];
            Restaurants::whereId(session()->get("restaurant.id"))->update([
                "logo_name" => null,            
             ]);
        }
        if($info->banner!=null){
            $newData['banner'] = $info->banner['dataURL'];
        }
        $result = Restaurants::whereId(session()->get("restaurant.id"))->update($newData);

        if(!$result){
            return response()->json(["title" => "Erro", "message" => "Erro a guardar as novas informações"], 500);
        }
        return response()->json(["title" => "Sucesso", "message" => "Informação guardada com sucesso!"]);
    }

    /**
     * For dropzone.js
     * 
     * @param file UploadedFile
     * @return ImageName
     */
    function fileupload(Request $data)
    {
        $image = $data->file('file');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('img/pfp'), $imageName);
        Users::whereId(session()->get('user')['id'])->update([
            "pfp" => $imageName
        ]);
        session()->put('user.pfp', $imageName);
        return response()->json(['success' => $imageName]);
    }

    /**
     * Update user settings
     * 
     * @param update boolean
     * @param updatePassword boolean
     * @param values
     * @return status
     */
    function update(Request $request)
    {
        if (!AppHelper::hasLogin()) return redirect("/");
        if ($request->update) {
            // Check if any of the values are empty
            if (AppHelper::hasEmpty($request->values)) {
                return response()->json(["status" => "Erro", "message" => "Preencha todos os campos"], 400);
            }

            // Check if email is valid
            if (!filter_var($request->values[3], FILTER_VALIDATE_EMAIL)) {
                return response()->json(["status" => "Erro", "message" => "Email invalido!"], 400);
            }

            // Update info
            $userInfoUpdate = Users::whereId(session()->get('user.id'))->update([
                "first_name" => $request->values[0],
                "last_name" => $request->values[1],
                "birthdate" => $request->values[2],
                "email" => $request->values[3],
            ]);

            $session_data = [
                'id' => session()->get('user.id'),
                'firstName' => $request->values[0],
                'lastName' => $request->values[1],
                'birthdate' => $request->values[2],
                'email' => $request->values[3],
                'isProfessional' => session()->get('user.isProfessional'),
                'pfp' => session()->get('user.pfp'),
                'active' => session()->get('user.active')
            ];

            // Update session
            session(["user" => $session_data]);
        }

        if ($request->updatePsw) {
            // Check if oldPsw matches user password
            $userPsw = UserAuth::where('user_id', session()->get('user.id'))->get()->first();
            if (!password_verify($request->passwords['oldPsw'], $userPsw->password)) {
                return response()->json(["status" => "Erro", "message" => "Password errada"], 400);
            }
            // Update db with new password
            $updatePsw = UserAuth::where('user_id', session()->get('user.id'))->update([
                "password" => password_hash($request->passwords['newPsw'], PASSWORD_DEFAULT)
            ]);
        }

        $showMessage = false;
        if (isset($updatePsw) || isset($userInfoUpdate)) {
            $showMessage = true;
        }

        if ($showMessage) {
            return response()->json(["status" => "Sucesso", "message" => "Atualizado com sucesso"], 200);
        } else {
            return response()->json(["status" => "", "message" => ""], 200);
        }
    }

    /**
     * Transfers owner ship of a restaurant from a user to another
     * 
     * @param \Illuminate\http\Request userIds
     * @return \Illuminate\http\Response status
     */
    function transferOwnerShip(Request $req)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }

        $ownerID = Types::where("restaurant_id", session()->get("restaurant.id"))->where("label", "Owner")->get()->first();

        // updates the new user's type to be owner
        $update = UsersTypes::where("user_id", $req->newOwner)->update([
            "type_id" => $ownerID->id
        ]);

        if (!$update) {
            return response()->json(["status" => "Erro", "message" => "Erro a dar owner ao user"], 400);
        }

        // remove old owner from restaurant
        $removeType = UsersTypes::where("user_id", session()->get("user.id"))->delete();

        if (!$removeType) {
            return response()->json(["status" => "Erro", "message" => "Erro ao retirar-lhe owner"], 400);
        }

        $remUser = UserRestaurant::where("user_id", session()->get("user.id"))->delete();

        if (!$remUser) {
            return response()->json(["status" => "Erro", "message" => "Erro ao retirar-lhe owner"], 400);
        }

        $setProZero = Users::whereId(session()->get("user.id"))->update([
            "isProfessional" => 0
        ]);

        if (!$setProZero) {
            return response()->json(["status" => "Erro", "message" => "Erro retirar-lhe de profissional"], 400);
        }

        AppHelper::createNotification($ownerID->id, "As suas permissões foram atualizadas para Owner. Deia logout para ativar estas mudanças");

        AppHelper::logout();

        return response()->json(["status" => "Sucesso", "message" => "transferido com sucesso"], 200);
    }
}
