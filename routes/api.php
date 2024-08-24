<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('products', ProductController::class);
Route::apiResource('carts', CartController::class);


// Route::post('/carts', [CartController::class, 'store']);



Route::post('http://192.168.56.1:8000/api/carts', function (Request $request) {
    $productId = $request->input('product_id');
    $quantity = $request->input('quantity');
    $productPrice = $request->input('product_price');

    // Get customer id
    $customerId = Auth::id(); // Mendapatkan customer id dari pengguna yang sudah login

    if (!$customerId) {
        // Jika pengguna belum login, gunakan IP address sebagai identifikasi
        $ip = $request->ip();
        $customerId = DB::table('anonymous_users')
            ->where('ip_address', $ip)
            ->value('id');

        if (!$customerId) {
            // Jika belum ada, buat entri baru untuk pengguna anonim
            $customerId = DB::table('anonymous_users')
                ->insertGetId(['ip_address' => $ip]);
        }
    }

    // Hitung subtotal
    $subtotal = $quantity * $productPrice;

    // Simpan ke tabel cart
    DB::table('carts')->insert([
        'customer_id' => $customerId,
        'product_id' => $productId,
        'quantity' => $quantity,
        'product_price' => $productPrice,
        'subtotal' => $subtotal,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['message' => 'Product added to cart successfully']);
});



Route::post('/cart', function (Request $request) {
    $request->validate([
        'customer_id' => 'required|string',
        'product_id' => 'required|integer',
        'quantity' => 'required|integer',
        'product_price' => 'required|numeric',
    ]);

    $cart = Cart::create([
        'customer_id' => $request->customer_id,
        'product_id' => $request->product_id,
        'quantity' => $request->quantity,
        'product_price' => $request->product_price,
        'subtotal' => $request->product_price * $request->quantity,
    ]);

    return response()->json($cart, 201);
});




