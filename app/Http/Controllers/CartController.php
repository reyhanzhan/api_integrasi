<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::all();
        return response()->json($cart);
    }

    public function show($id)
    {
        $cart = Cart::find($id);
        if ($cart) {
            return response()->json($cart);
        } else {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $customerId = $request->user() ? $request->user()->id : $request->session()->get('ip_address');
        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');
        $product_price = $request->input('product_price');

        $cart = new Cart();
        $cart->customer_id = $customerId;
        $cart->product_id = $product_id;
        $cart->quantity = $quantity;
        $cart->product_price = $product_price;
        $cart->subtotal = $quantity * $product_price;
        $cart->save();

        return response()->json(['message' => 'Product added to cart successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::find($id);
        if ($cart) {
            $cart->update($request->all());
            return response()->json($cart);
        } else {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    }

    public function destroy($id)
    {
        $cart = Cart::find($id);
        if ($cart) {
            $cart->deleted = true;
            $cart->save();
            return response()->json(['message' => 'Cart deleted']);
        } else {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    }
}
