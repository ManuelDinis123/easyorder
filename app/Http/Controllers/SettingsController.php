<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\UserAuth;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");

        return view("frontend/professional/settings/user");
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
        if (!AppHelper::checkAuth()) return redirect("/no-access");
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
                "birthdate" => date("Y-m-d", strtotime($request->values[2])),
                "email" => $request->values[3],
            ]);

            $session_data = [
                'id' => session()->get('user.id'),
                'firstName'=> $request->values[0],
                'lastName' => $request->values[1],
                'birthdate' => $request->values[2],
                'email' => $request->values[3],
                'isProfessional' => session()->get('user.isProfessional'),
                'pfp' => session()->get('user.pfp')
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
}
