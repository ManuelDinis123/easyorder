<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Orders;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    // Display the stats view and get the statistics data
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");
        if (!AppHelper::checkUserType(session()->get("type.id"), 'owner') || !AppHelper::checkUserType(session()->get("type.id"), 'admin')) {
            if (!AppHelper::checkUserType(session()->get("type.id"), 'view_stats')) return redirect("/professional");
        }


        // 1. Profit (bar chart)
        // Weekly
        $last_week_days = $this::get_profit("last");
        $this_week_days = $this::get_profit("this");
        // Monthly
        $last_month_days = $this::get_profit("last", "monthly");
        $this_month_days = $this::get_profit("this", "monthly");
        // Yearly
        $last_year_days = $this::get_profit("last", "yearly");
        $this_year_days = $this::get_profit("this", "yearly");

        // 2. Get the review avg
        // Daily
        $thisStats = AppHelper::calculateReviewAvg(session()->get('restaurant.id'), ["time" => "week", "which" => "this"]);
        $lastStats = AppHelper::calculateReviewAvg(session()->get('restaurant.id'), ["time" => "week", "which" => "last"]);
        // Month
        $thisStatsMONTH = AppHelper::calculateReviewAvg(session()->get('restaurant.id'), ["time" => "month", "which" => "this"]);
        $lastStatsMONTH = AppHelper::calculateReviewAvg(session()->get('restaurant.id'), ["time" => "month", "which" => "last"]);
        // Year
        $thisStatsYEAR = AppHelper::calculateReviewAvg(session()->get('restaurant.id'), ["time" => "year", "which" => "this"]);
        $lastStatsYEAR = AppHelper::calculateReviewAvg(session()->get('restaurant.id'), ["time" => "year", "which" => "last"]);

        // 3. Get gain
        // Daily
        $thisGain = $this::get_lucro("this");
        $lastGain = $this::get_lucro("last");
        // Month
        $thisGainMONTH = $this::get_lucro("this", "month");
        $lastGainMONTH = $this::get_lucro("last", "month");
        // Year
        $thisGainYEAR = $this::get_lucro("this", "year");
        $lastGainYEAR = $this::get_lucro("last", "year");

        // TODO: Refactor this. Create 3 arrays for the week, month and year info
        return view("frontend/professional/stats/stats")
            ->with("lastPerDay", $last_week_days)
            ->with("thisPerDay", $this_week_days)
            ->with("lastPerMonth", $last_month_days)
            ->with("thisPerMonth", $this_month_days)
            ->with("lastPerYear", $last_year_days)
            ->with("thisPerYear", $this_year_days)
            ->with('lastRev', $lastStats)
            ->with('thisRev', $thisStats)
            ->with('lastRevMonth', $lastStatsMONTH)
            ->with('thisRevMonth', $thisStatsMONTH)
            ->with('lastRevYear', $lastStatsYEAR)
            ->with('thisRevYear', $thisStatsYEAR)
            ->with('thisGain', $thisGain)
            ->with('lastGain', $lastGain)
            ->with('thisGainMonth', $thisGainMONTH)
            ->with('lastGainMonth', $lastGainMONTH)
            ->with('thisGainYear', $thisGainYEAR)
            ->with('lastGainYear', $lastGainYEAR);
    }

    static function get_lucro($week, $timeline = "daily")
    {
        // Get the start and end dates of the current week
        if ($timeline == "daily") {
            $startDate = new DateTime('monday ' . $week . ' week');
            $endDate = new DateTime('sunday ' . $week . ' week');
        } else if ($timeline == "month") {
            $startDate = new DateTime('first day of ' . $week . ' month');
            $endDate = new DateTime('last day of ' . $week . ' month');
        } else {
            $startDate = new DateTime('first day of January ' . $week . ' year');
            $endDate = new DateTime('last day of December ' . $week . ' year');
        }

        // Format the dates as strings in the format expected by whereBetween
        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');

        $lucro = Orders::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('order_items_sides', 'order_items_sides.order_item_id', '=', 'order_items.id')
            ->join('menu_item_ingredients', 'menu_item_ingredients.id', '=', 'order_items_sides.side_id')
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->where('orders.restaurant_id', '=', session()->get("restaurant.id"))
            ->where('orders.closed', '=', 1)
            ->whereBetween('orders.deadline', [$startDateStr, $endDateStr])
            ->selectRaw('(SUM((menu_item.price * order_items.quantity)) - SUM(menu_item.cost)) + SUM(menu_item_ingredients.price * order_items_sides.quantity) as lucro')
            ->get()->first();


        $avgRendimento = Orders::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('order_items_sides', 'order_items_sides.order_item_id', '=', 'order_items.id')
            ->join('menu_item_ingredients', 'menu_item_ingredients.id', '=', 'order_items_sides.side_id')
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->where('orders.restaurant_id', '=', session()->get("restaurant.id"))
            ->where('orders.closed', '=', 1)
            ->whereBetween('orders.deadline', [$startDateStr, $endDateStr])
            ->selectRaw('round((SUM((menu_item.price * order_items.quantity)) + SUM(menu_item_ingredients.price * order_items_sides.quantity)) / 7) as avgLucro')
            ->get()->first();

        $avgCost = Orders::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->where('orders.restaurant_id', '=', session()->get("restaurant.id"))
            ->where('orders.closed', '=', 1)
            ->whereBetween('orders.deadline', [$startDateStr, $endDateStr])
            ->selectRaw('round(SUM((menu_item.cost * order_items.quantity)) / 7) as avgCost')
            ->get()->first();

        $allData = [
            "total_gain" => $lucro->lucro,
            "avg_per_day" => $avgRendimento->avgLucro,
            "avg_cost_per_day" => $avgCost->avgCost
        ];

        return $allData;
    }

    static function get_profit($timeframe, $timing = "daily")
    {
        $start_datetime_query = ($timing == "daily" ? "monday " . $timeframe . " week" : ($timing == "monthly" ? "first day of " . $timeframe . " month" : "first day of january " . $timeframe . " year"));
        $end_datetime_query = ($timing == "daily" ? "sunday " . $timeframe . " week" : ($timing == "monthly" ? "last day of " . $timeframe . " month" : "last day of december " . $timeframe . " year"));

        // Get the start and end dates of the current week
        $startDate = new DateTime($start_datetime_query);
        $endDate = new DateTime($end_datetime_query);

        // Format the dates as strings in the format expected by whereBetween
        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');

        $profitperday_raw = Orders::select(DB::raw('DATE(orders.deadline) as date, (sum((menu_item.price * order_items.quantity))+sum(menu_item_ingredients.price*order_items_sides.quantity)) as total_price'))
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('order_items_sides', 'order_items_sides.order_item_id', '=', 'order_items.id')
            ->join('menu_item_ingredients', 'menu_item_ingredients.id', '=', 'order_items_sides.side_id')
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->where('orders.restaurant_id', '=', session()->get("restaurant.id"))
            ->where('orders.closed', '=', 1)
            ->whereBetween('orders.deadline', [$startDateStr, $endDateStr])
            ->groupBy('orders.id', DB::raw('DATE(orders.deadline)'))
            ->get();

        $profitpDay = $profitperday_raw->groupBy('date')->map(function ($group) {
            return [
                'date' => $group[0]->date,
                'total_price' => $group->sum('total_price'),
            ];
        })->values();

        $dates = [];
        $days_map = [];
        if ($timing == "daily") {
            $days_map = [
                "Segunda-Feira",
                "Terça-Feira",
                "Quarta-Feira",
                "Quinta-Feira",
                "Sexta-Feira",
                "Sábado",
                "Domingo",
            ];
        } else if ($timing == "monthly") {
            // 31 is the max month length. But it doesn't mean the final array will have 31 days for every month.
            // When the month doesn't have more days it will stop iterating the array that is being created here.
            for ($i = 0; $i < 31; $i++) {
                $days_map[] = $i + 1;
            }
        } else {
            $days_map = range(1, 31);
            $months_map = range(1, 12);
            $aux_day = 0;
            $aux_month = 0;
        }
        $aux = 0;
        while ($startDate <= $endDate) {

            $priceForThisDate = 0;
            foreach ($profitpDay as $val) {
                if ($val['date'] == $startDate->format('Y-m-d')) {
                    $priceForThisDate = $val['total_price'];
                    break;
                }
            }

            if ($timing != "yearly") {
                // Add the current date to the array
                $dates[$startDate->format('Y-m-d')] = [
                    'date' => $startDate->format('Y-m-d'),
                    'day' => $days_map[$aux],
                    'total_price' => $priceForThisDate
                ];
            } else {
                if ($startDate->format('m') != $aux_month) {
                    $dates[$startDate->format('Y-m')] = [];
                    $aux_month = $startDate->format('m');
                }
                $dates[$startDate->format('Y-m')][] = [
                    'date' => $startDate->format('Y-m-d'),
                    'day' => $days_map[$aux_day],
                    'total_price' => $priceForThisDate
                ];
            }

            // Increment the date by one day
            $startDate->modify('+1 day');
            $aux++;
        }

        // If it's comparing to the whole month format the array to have all week data summed by week
        if ($timing == "monthly") {
            $aux = 1;
            $week = 1;
            $sum = 0;
            $dates_aux = [];
            foreach ($dates as $key => $val) {
                if ($aux < 7) {
                    $sum += $val['total_price'];
                    $dates_aux["week" . $week] = ["day" => "week " . $week, "total_price" => $sum];
                    $aux++;
                } else {
                    $sum += $val['total_price'];
                    $dates_aux["week" . $week] = ["day" => "week " . $week, "total_price" => $sum];
                    $aux = 1;
                    $sum = 0;
                    $week++;
                }
            }
            $dates = $dates_aux;
        } else if ($timing == "yearly") {
            foreach ($dates as $key => $val) {
                $dates[$key] =  ["total_price" => array_sum(array_column($val, 'total_price'))];
            }
        }

        return $dates;
    }
}
