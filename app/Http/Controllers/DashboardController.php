<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\AppHelper;
use App\Models\Orders;
use DateTime;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function index()
    {
        if (!AppHelper::checkAuth()) return redirect("/no-access");

        $startDate = new DateTime('first day of this month');
        $endDate = new DateTime('last day of this month');

        $startDateStr = $startDate->format('Y-m-d') . " 00:00:00";
        $endDateStr = $endDate->format('Y-m-d') . " 23:59:59";

        $avgRendimento = Orders::select(DB::raw('round((SUM((order_items.price * order_items.quantity)) + COALESCE(side_dishes_sum, 0)) / 7) as avgLucro'))
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->where('orders.restaurant_id', '=', session()->get("restaurant.id"))
            ->where('orders.closed', '=', 1)
            ->whereBetween('orders.deadline', [$startDateStr, $endDateStr])
            ->leftJoin(DB::raw('(SELECT order_item_id, SUM(order_items_sides.price * order_items_sides.quantity) as side_dishes_sum FROM order_items_sides JOIN menu_item_ingredients ON order_items_sides.side_id = menu_item_ingredients.id GROUP BY order_item_id) as sides'), 'sides.order_item_id', '=', 'order_items.id')
            ->get()->first();

        $avgCost = Orders::join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->where('orders.restaurant_id', '=', session()->get("restaurant.id"))
            ->where('orders.closed', '=', 1)
            ->whereBetween('orders.deadline', [$startDateStr, $endDateStr])
            ->selectRaw('round(SUM((order_items.cost * order_items.quantity)) / 7) as avgCost')
            ->get()->first();

        $lucro = Orders::select(DB::raw('(SUM((order_items.price * order_items.quantity)) - SUM(order_items.cost)) + COALESCE(side_dishes_total, 0) as lucro'))
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_item', 'menu_item.id', '=', 'order_items.menu_item_id')
            ->where('orders.restaurant_id', '=', session()->get("restaurant.id"))
            ->where('orders.closed', '=', 1)
            ->whereBetween('orders.deadline', [$startDateStr, $endDateStr])
            ->leftJoin(DB::raw('(SELECT order_item_id, SUM(order_items_sides.price * order_items_sides.quantity) as side_dishes_total FROM order_items_sides JOIN menu_item_ingredients ON order_items_sides.side_id = menu_item_ingredients.id GROUP BY order_item_id) as sides'), 'sides.order_item_id', '=', 'order_items.id')
            ->get()->first();

        $meses = [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ];

        $month = [
            "month" => $meses[date('m') - 1],
            "rendimento" => $avgRendimento['avgLucro'],
            "despesas" => $avgCost['avgCost'],
            "lucro" => $lucro['lucro']
        ];

        return view('frontend/professional/dashboard')
            ->with("month", $month);
    }
}
