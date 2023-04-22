<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Reports;
use App\Models\Restaurants;
use App\Models\Reviews;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Admin page
     * 
     * @return \Illuminate\View
     */
    function index()
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $userCount = Users::count();
        $restaurantCount = Restaurants::count();
        $denunciasCount = Reports::count();

        return view("admin.dashboard")
            ->with("users", $userCount)
            ->with("rest", $restaurantCount)
            ->with("den", $denunciasCount);
    }

    /**
     * Admin restaurant page
     * 
     * @return \Illuminate\View
     */
    function restaurant()
    {
        if (!AppHelper::app_admin()) return redirect("/");

        return view("admin.restaurants");
    }

    /**
     * Admin users page
     * 
     * @return \Illuminate\View
     */
    function users()
    {
        if (!AppHelper::app_admin()) return redirect("/");

        return view("admin.users");
    }

    /**
     * Admin reports page
     * 
     * @return \Illuminate\View
     */
    function reports()
    {
        if (!AppHelper::app_admin()) return redirect("/");

        return view("admin.reports");
    }

    /**
     * Gets the review reports
     * 
     * @return \Illuminate\Http\Response
     */
    function getReports()
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $reports = Reports::select("reviews.id", DB::raw("reports.id as reportid"), DB::raw("CONCAT(users.first_name, ' ', users.last_name) as user"), "reviews.title", "reviews.review", "reviews.stars", "reviews.written_at", "reports.description")
            ->join("reviews", "reviews.id", "=", "reports.review_id")
            ->join("users", "users.id", "=", "reviews.written_by")
            ->get();

        return response()->json($reports);
    }

    /**
     * Ignore a report
     * 
     * @return \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    function ignoreReport(Request $id)
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $res = Reports::whereId($id->report_id)->delete();

        if (!$res) return response()->json(["title" => "Erro", "message" => "ocorreu um erro a ignorar esta denúncia"], 500);
        return response()->json(["title" => "Sucesso", "message" => "ignorado com sucesso"]);
    }

    /**
     * Remove a report and the delete the review connected to it
     * 
     * @return \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    function removeReport(Request $id)
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $res = Reports::whereId($id->report_id)->delete();

        if (!$res) {
            if (!$res) return response()->json(["title" => "Erro", "message" => "ocorreu um erro a remover esta review"], 500);
        }

        $res2 = Reviews::whereId($id->review_id)->delete();

        if (!$res2) return response()->json(["title" => "Erro", "message" => "ocorreu um erro a remover esta review"], 500);
        return response()->json(["title" => "Sucesso", "message" => "Removido com sucesso"]);
    }

    /**
     * Gets all users in the platform
     * 
     * @return \Illuminate\Http\Response Users
     */
    function getUsers()
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $users = Users::select(
            [
                "id",
                DB::raw("concat(first_name, ' ', last_name) as name"),
                "email",
                "birthdate",
                "isProfessional",
                "active",
                "app_admin",
                "banned"
            ]
        )->get();

        return response()->json($users);
    }

    /**
     * Switches user to an app admin or not
     * 
     * @param \Illuminate\Http\Request UserID
     * @return \Illuminate\Http\Response Status
     */
    function switchAppAdmin(Request $req)
    {
        if (!AppHelper::app_admin()) return redirect("/");
        $update = Users::whereId($req->user_id)
            ->update([
                "app_admin" => $req->active
            ]);
        if (!$update) return response()->json(["title" => "Erro", "message" => "Erro a " . ($req->active ? "tornar o user em admin" : "remover o user de admin")], 500);
        return response()->json(["title" => "Sucesso", "message" => "User " . ($req->active ? "tornado em admin" : "removido de admin") . " com sucesso"]);
    }

    /**
     * Bans / Unbans users
     * 
     * @param \Illuminate\Http\Request UserID
     * @return \Illuminate\Http\Response Status
     */
    function banUser(Request $req)
    {
        if (!AppHelper::app_admin()) return redirect("/");
        $update = Users::whereId($req->user_id)
            ->update([
                "banned" => $req->active
            ]);
        if (!$update) return response()->json(["title" => "Erro", "message" => "Erro a " . ($req->active ? "banir o user" : "desbanir o user")], 500);
        return response()->json(["title" => "Sucesso", "message" => "User " . ($req->active ? "banir o user" : "desbanir o user") . " com sucesso"]);
    }

    /**
     * Delete review
     * 
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    function deleteReview(Request $id)
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $res = Reviews::whereId($id->id)->remove();

        if (!$res) return response()->json(["title" => "Erro", "message" => "ocorreu um erro a ignorar esta denúncia"], 500);
        return response()->json(["title" => "Sucesso", "message" => "ignorado com sucesso"]);
    }

    /**
     * Get all restaurants
     * 
     *
     * @param \Illuminate\Http\Response
     */
    function getRestaurants()
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $restaurants = Restaurants::select(
            "restaurants.id",
            "restaurants.name",
            "restaurants.description",
            DB::raw("concat(users.first_name, ' ', users.last_name) as owner"),
            "restaurants.active"
        )
            ->join("user_restaurant", "user_restaurant.restaurant_id", "=", "restaurants.id")
            ->join("users", "users.id", "=", "user_restaurant.user_id")
            ->join("types", "types.restaurant_id", "=", "users.id")
            ->join("users_types", "users_types.type_id", "=", "types.id")
            ->where("types.label", "Owner")
            ->get();

        return response()->json($restaurants);
    }

    /**
     * Switch restaurant to either active or deactivated
     * 
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    function switchRestaurant(Request $req)
    {
        if (!AppHelper::app_admin()) return redirect("/");

        $update = Restaurants::whereId($req->id)->update([
            "active" => $req->active
        ]);

        if (!$update) return response()->json(["title" => "Erro", "message" => "ocorreu um erro a " . ($req->active ? "ativar" : "desativar") . " o restaurante"], 500);
        return response()->json(["title" => "Sucesso", "message" => "restaurante " . ($req->active ? "ativado" : "desativado") . " com sucesso"]);
    }
}
