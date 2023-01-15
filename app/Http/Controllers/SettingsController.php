<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\UserAuth;
use App\Models\Users;
use Illuminate\Http\Request;
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
    function fileupload(Request $data) {
        $image = $data->file('file');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('img/pfp'), $imageName);
        Users::whereId(session()->get('user')['id'])->update([
            "pfp" => $imageName
        ]);
        session()->put('user.pfp', $imageName);
        return response()->json(['success'=>$imageName]);
    }
}
