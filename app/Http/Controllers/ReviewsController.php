<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Reports;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewsController extends Controller
{
    // Pro

    /**
     * Page to view all restaurant reviews 
     * 
     * @return \Illuminate\View
     */
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/s");

        // Get all reviews
        $reviews = Reviews::select("reviews.id", "reviews.stars", "reviews.written_at", "users.first_name", "users.last_name", "reviews.title", "reviews.review", "users.pfp")
            ->where("restaurant_id", session()->get("restaurant.id"))
            ->join("users", 'users.id', '=', 'reviews.written_by')
            ->orderBy("reviews.written_at", "DESC")
            ->get();

        return view("frontend.professional.reviews.reviews")->with("reviews", $reviews);
    }

    /**
     * Report a review
     * 
     * @param \Illuminate\Http\Request req
     * @return \Illuminate\Http\Response status
     */
    function report_review(Request $req)
    {
        if (!AppHelper::checkAuth()) return redirect("/");
        if ((!AppHelper::checkUserType(session()->get("type.id"), ['owner', 'admin'], false))) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_menu')) return redirect("/professional");
        }

        $reported = Reports::create([
            "review_id" => $req->review,
            "description" => $req->description,
        ]);

        if(!$reported) return response()->json(["title" => "Erro", "message" => "Erro a reportar a crítica"], 500);

        return response()->json(["title" => "Sucesso", "message" => "Crítica enviada"]);
    }

    // Clients

    /**
     * Add reviews
     * 
     * @return Response
     */
    function add(Request $request)
    {
        if (!AppHelper::hasLogin()) return redirect("/");
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

    /**
     * Delete user review
     * 
     * 
     * @return Response
     */
    function deleteReview(Request $request)
    {
        if (!AppHelper::hasLogin()) return redirect("/");
        $action = Reviews::whereId($request->id)->delete();

        if (!$action) return response()->json(["title" => "Erro", "message" => "Erro ao apagar review"], 500);

        return response()->json(["title" => "Sucesso", "message" => "Critica removida com sucesso!"], 200);
    }
}
