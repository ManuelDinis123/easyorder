<?php

namespace App\Http\Controllers;

use App\Models\CartItems;
use App\Models\Shoppingcart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
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
            ]);
        } else {
            $addition = CartItems::whereId($exists->id)->update([
                "quantity" => ($exists->quantity + 1)
            ]);
        }

        if (!$addition) return 0;

        return 1;
    }
}
