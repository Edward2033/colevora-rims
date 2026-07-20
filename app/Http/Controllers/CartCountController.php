<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\JsonResponse;

class CartCountController extends Controller
{
    public function __invoke(): JsonResponse
    {
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->where('status', 'active')->first();
            return response()->json(['count' => $cart ? $cart->items()->sum('quantity') : 0]);
        }

        $sessionCart = session()->get('guest_cart', []);
        $count = array_sum(array_column($sessionCart, 'quantity'));

        return response()->json(['count' => $count]);
    }
}
