<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewsController extends Controller
{
    /**
     * Add reviews
     * 
     * @return Response
     */
    function add(Request $request)
    {

        if ($request->edit) {
            $update = Reviews::whereId($request->id)->update([
                "written_by" => session()->get("user.id"),
                "restaurant_id" => $request->restaurant_id,
                "title" => $request->title,
                "review" => $request->body,
                "stars" => $request->stars,
                "written_at" => today()
            ]);

            if (!$update) return response()->json(["title" => "Erro", "message" => "Erro ao editar critica"], 500);

            return response()->json(["title" => "Sucesso", "message" => "Critica editada!"], 200);
        }

        $insert = Reviews::create([
            "written_by" => session()->get("user.id"),
            "restaurant_id" => $request->restaurant_id,
            "title" => $request->title,
            "review" => $request->body,
            "stars" => $request->stars,
            "written_at" => today()
        ]);

        if (!$insert) return response()->json(["title" => "Erro", "message" => "Erro ao enviar critica"], 500);

        return response()->json(["title" => "Sucesso", "message" => "Critica enviada!"], 200);
    }
}
