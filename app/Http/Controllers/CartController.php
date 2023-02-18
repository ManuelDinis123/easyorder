<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\CartItems;
use App\Models\Ingredients;
use App\Models\MenuItems;
use App\Models\Shoppingcart;
use App\Models\SideDishes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

    /**
     * Shopping cart overview page
     * 
     * @return view
     */
    function index()
    {
        if (!AppHelper::hasLogin()) return redirect("/");

        // Get which restaurants are associated with items in cart
        $restaurants = CartItems::select("restaurants.id", "restaurants.name")
            ->join("menu_item", "menu_item.id", "=", "cart_items.item_id")
            ->join("menu", "menu.id", "=", "menu_item.menu_id")
            ->join("restaurants", "restaurants.id", "=", "menu.restaurant_id")
            ->where("cart_items.cart_id", session()->get("shoppingCart"))
            ->distinct()
            ->get();

        $final_data = [];
        foreach ($restaurants as $restaurant) {
            $final_data[$restaurant['id']] = ["id" => $restaurant['id'], "name" => $restaurant['name'], "items" => []];
        }

        // Get items in cart
        $items = CartItems::select(DB::raw("restaurants.id as res_id"), DB::raw("cart_items.id as cart_item_id"), "cart_items.note", "cart_items.item_id", "cart_items.quantity", "menu_item.name", "menu_item.price", "menu_item.description", "menu_item.imageUrl")
            ->join("menu_item", "menu_item.id", "=", "cart_items.item_id")
            ->join("menu", "menu.id", "=", "menu_item.menu_id")
            ->join("restaurants", "restaurants.id", "=", "menu.restaurant_id")
            ->where("cart_items.cart_id", session()->get("shoppingCart"))
            ->get();

        $count = 0;
        foreach ($items as $item) {
            $count += $item['quantity'];
            $final_data[$item['res_id']]['items'] += [
                $item['item_id'] => [
                    "item_id" => $item['item_id'],
                    "cart_item_id" => $item['cart_item_id'],
                    "note" => $item['note'],
                    "name" => $item['name'],
                    "quantity" => $item['quantity'],
                    "price" => $item['price'],
                    "description" => $item['description'],
                    "imageUrl" => $item['imageUrl'],
                ]
            ];
        }

        return view("frontend.cart.overview")->with("cart", $final_data)->with('count', $count);
    }

    /**
     * Creates a cart for user or adds stuff to the cart
     * 
     * @return response
     */
    function addToCart(Request $data)
    {
        if (!session()->get('shoppingCart')) {
            $this->createCart(session()->get("user.id"));
        }

        $addition = $this->addItems($data->isRemove, session()->get("shoppingCart"), $data->item_id);

        if (!$addition) return response()->json(["title" => "Erro", "message" => "Erro a adicionar ao carrinho!"], 500);

        if ($addition == "deleted") return "deleted";

        return response()->json(["title" => "Sucesso", "message" => "Adicionado ao carrinho com sucesso!"], 200);
    }

    /**
     * Get all items in card
     * 
     * @return Array
     */
    function get(Request $data)
    {
        if (!session()->get('shoppingCart')) {
            return "no items found...";
        }

        if (isset($data->restaurantID)) {
            $res = CartItems::select("menu_item.id", "menu_item.name", "menu_item.price", "cart_items.quantity")
                ->join("menu_item", "menu_item.id", "=", "cart_items.item_id")
                ->join("menu", "menu.id", "=", "menu_item.menu_id")
                ->where("cart_items.cart_id", session()->get('shoppingCart'))
                ->where("menu.restaurant_id", $data->restaurantID)
                ->get();
        } else {
            $res = CartItems::select("menu_item.id", "menu_item.name", "menu_item.price", "cart_items.quantity")
                ->join("menu_item", "menu_item.id", "=", "cart_items.item_id")
                ->join("menu", "menu.id", "=", "menu_item.menu_id")
                ->where("cart_items.cart_id", session()->get('shoppingCart'))
                ->get();
        }

        return response()->json($res, 200);
    }

    // Create a cart
    function createCart($userID)
    {
        $new = Shoppingcart::create([
            "user_id" => $userID
        ]);

        session(['shoppingCart' => $new->id]);

        if (!$new) return false;

        return true;
    }

    // adds or removes items from cart
    function addItems($isRemove, $cartID, $itemID)
    {
        if ($isRemove) {
            $exists = CartItems::where("item_id", $itemID)->get()->first();
            if (($exists->quantity - 1) == 0) {
                $remove = CartItems::where("item_id", $itemID)->delete();
                if (!$remove) return 0;

                return "deleted";
            } else {
                $remove = CartItems::whereId($exists->id)->update([
                    "quantity" => ($exists->quantity - 1)
                ]);
                if (!$remove) return 0;

                return 1;
            }
        }

        $exists = CartItems::where("item_id", $itemID)->get()->first();

        if (is_null($exists)) {
            $addition = CartItems::create([
                "item_id" => $itemID,
                "quantity" => 1,
                "cart_id" => $cartID,
                "note" => ""
            ]);
        } else {
            $addition = CartItems::whereId($exists->id)->update([
                "quantity" => ($exists->quantity + 1)
            ]);
        }

        if (!$addition) return 0;

        return 1;
    }

    /**
     *  Get item notes
     * 
     *  @return Response
     */
    function getNote(Request $id)
    {
        $note = CartItems::select("note")->whereId($id->id)->get()->first();

        return response($note, 200);
    }

    /**
     *  Add notes to items
     * 
     *  @return Response
     */
    function addNotes(Request $note)
    {
        $addNote = CartItems::whereId($note->cart_item_id)->update([
            "note" => isset($note->note) ? $note->note : ""
        ]);

        if (!$addNote) return response()->json(["title" => "Erro", "message" => "Erro a adicionar nota"], 200);

        return response()->json(["title" => "Sucesso", "message" => "Nota adicionada"], 200);
    }

    /**
     * Add side dishes to cart item in DB
     * 
     * 
     * @return response
     */
    function addSides(Request $data)
    {
        if($data->quantity == 0) {
            SideDishes::whereId($data->sdID)->delete();
            return response("removed", 200);
        }

        if (isset($data->sdID)) {
            SideDishes::whereId($data->sdID)->update([
                "side_id" => $data->side_id,
                "quantity" => $data->quantity,
                "cart_item_id" => $data->cart_item_id,
            ]);
            $side = SideDishes::whereId($data->sdID)->get()->first();
        } else {
            $side = SideDishes::create([
                "side_id" => $data->side_id,
                "quantity" => $data->quantity,
                "cart_item_id" => $data->cart_item_id,
            ]);
        }

        if (!$side) return response()->json(["title" => "Erro", "message" => "Erro a adicionar o acompanhamento"], 200);

        return response()->json(["id" => $side->id, "quantity" => $side->quantity], 200);
    }

    function getSides(Request $data) {

        $cart_id = session()->get("shoppingCart");

        $ingredients = Ingredients::select(
            "menu_item_ingredients.id",
            "menu_item_ingredients.ingredient",
            "side_dishes.quantity",
            "menu_item_ingredients.quantity_type",
        )
        ->leftJoin('side_dishes', 'side_dishes.side_id', '=', 'menu_item_ingredients.id')
        ->leftJoin('cart_items', 'cart_items.id', '=', 'side_dishes.cart_item_id')
        ->where(function ($query) use ($cart_id) {
            $query->where('cart_items.cart_id', $cart_id)
                ->orWhereNull('cart_items.cart_id');
        })
        ->where('menu_item_ingredients.menu_item_id', $data->id)
        ->get();
    

        return response()->json($ingredients, 200);
    }
}
