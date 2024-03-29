<?php

namespace App\Helpers;

use App\Models\Activity;
use App\Models\CartItems;
use App\Models\Notifications;
use App\Models\Orders;
use App\Models\Reviews;
use App\Models\Shoppingcart;
use App\Models\SideDishes;
use App\Models\Types;
use App\Models\Users;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppHelper
{

    /**
     *  Checks if the user is logged in
     * 
     * @return Boolean
     */
    public static function hasLogin()
    {
        return session()->get('authenticated');
    }

    /**
     *  Checks if the user is logged in an is a professional account
     * 
     * @return Boolean
     */
    public static function checkAuth()
    {
        return session()->get('authenticated') && (session()->get('user')['isProfessional'] && session()->get('user')['active']);
    }

    /**
     * Logs out the user
     * 
     * 
     * @return Boolean
     */
    public static function logout()
    {
        $logged = Users::whereId(session()->get('user.id'))->update([
            "logged_in" => 0
        ]);

        if (session()->get('shoppingCart')) {
            SideDishes::join('cart_items', 'cart_items.id', '=', "side_dishes.cart_item_id")
                ->where("cart_id", session()->get('shoppingCart'))->delete();
            CartItems::where("cart_id", session()->get('shoppingCart'))->delete();
            Shoppingcart::whereId(session()->get('shoppingCart'))->delete();
        }

        session()->flush();

        return true;
    }

    /**
     * Gets a id of the type and checks if user has certain permissions
     * set all to true if function should return false if any of the permissions fail
     * set all to false if only need one to return true
     * 
     * @param id integer
     * @param permissions Array
     * @param all Boolean
     * @return Boolean
     */
    public static function checkUserType($id, $permissions, $all = true)
    {
        // Get the type from the id
        $type = Types::whereId($id)->get()->first();

        $hasPerm = 0;

        // If the permissions var isn't an array an only one permission the loop isn't necessary so only do the if
        if (is_array($permissions)) {
            foreach ($permissions as $perm) {
                if ($type[$perm] == 0) {
                    if ($all) return 0;
                    $hasPerm = 0;
                } else if (!$all) {
                    $hasPerm = 1;
                    break;
                }
            }
        } else {
            if ($type[$permissions] == 0) {
                return 0;
            } else {
                return 1;
            }
        }

        return $hasPerm;
    }

    /**
     * Checks if values in a given array are empty or not
     * 
     * @return Boolean
     */
    public static function hasEmpty($values)
    {
        foreach ($values as $val) {
            if (!$val) {
                return true;
            }
        }
        return false;
    }

    /**
     * Clears shopping cart
     * 
     * @return Boolean
     */
    public static function clearCart()
    {
        $ci = CartItems::where('cart_id', session()->get('shoppingCart'))->get();
        foreach ($ci as $i) {
            SideDishes::where("cart_item_id", $i->id)->delete();
        }
        CartItems::where("cart_id", session()->get("shoppingCart"))->delete();
    }

    /**
     * Checks if user is an app admin or not
     * 
     * @return Boolean
     */
    public static function app_admin()
    {
        $user = Users::whereId(session()->get("user.id"))->get()->first();
        if (!$user->app_admin) {
            return false;
        }
        return true;
    }

    /**
     * Calculates the avg Reviews of a given restaurant
     * 
     * @param Int id
     * @param Array timestamp
     * @return Array
     */
    public static function calculateReviewAvg($id, $timestamp = ["time" => null, "which" => null])
    {
        if ($timestamp["time"] != null) {
            if ($timestamp["time"] == "week") {
                // Get the start and end dates of the current week
                $startDate = new DateTime('monday ' . $timestamp['which'] . ' week');
                $endDate = new DateTime('sunday ' . $timestamp['which'] . ' week');
            } else if ($timestamp["time"] == "month") {
                $startDate = new DateTime('first day of ' . $timestamp['which'] . ' month');
                $endDate = new DateTime('last day of ' . $timestamp['which'] . ' month');
            } else {
                $startDate = new DateTime('first day of January ' . $timestamp['which'] . ' year');
                $endDate = new DateTime('last day of December ' . $timestamp['which'] . ' year');
            }
            // Format the dates as strings in the format expected by whereBetween
            $startDateStr = $startDate->format('Y-m-d');
            $endDateStr = $endDate->format('Y-m-d');
        }

        // Average stars from the restaurant
        $avg_query = Reviews::where('restaurant_id', $id)
            ->selectRaw('ROUND(SUM(stars) / COUNT(id)) as avg');

        if ($timestamp['time'] != null) {
            $avg_query->whereBetween('reviews.written_at', [$startDateStr, $endDateStr]);
        }

        $avg = $avg_query->value('avg');

        $totalItems = Reviews::where('restaurant_id',  $id)
            ->count();

        // Get how many 5, 4, 3, 2, 1 stars reviews exist
        $srQ = Reviews::where('restaurant_id', $id)
            ->select('stars', DB::raw('round((COUNT(stars) / ' . $totalItems . ')*100) as count'));

        if ($timestamp['time'] != null) {
            $srQ->whereBetween('reviews.written_at', [$startDateStr, $endDateStr]);
        }

        $starReviews = $srQ->groupBy('stars')->get();

        $stats = ["stars" => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0,], "avg" => 0];
        foreach ($starReviews as $sr) {
            $stats['stars'][$sr['stars']] = $sr['count'];
        }

        $stats['avg'] = $avg;

        return $stats;
    }

    /**
     * Creates a notification for user
     * 
     * @param Int userId
     * @param String message
     * @return Boolean response
     */
    public static function createNotification($userId, $message)
    {
        $notify = Notifications::create([
            "user_id" => $userId,
            "message" => $message,
        ]);

        return $notify;
    }

    /**
     * Records an activity
     * 
     * @param String info
     * @param String link (optional)
     * @return Array response
     */
    public static function recordActivity($info, $link = null)
    {
        if (!$info) return ["message" => "Info is required", "status" => 400];
        $save = Activity::create([
            "user_id" => session()->get("user.id"),
            "restaurant_id" => session()->get("restaurant.id"),
            "info" => $info,
            "link" => $link ? $link : null,
            "created_at" => date('Y-m-d H:i:s')
        ]);
        if(!$save) return ["message" => "Error", "status" => 500];
        return ["message" => "Success", "status" => 200];
    }
}
