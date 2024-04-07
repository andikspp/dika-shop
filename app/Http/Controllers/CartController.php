<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Ambil carts hanya untuk pengguna yang login saat ini
        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->get();
        return view('back.pages.buyer.cart', compact('carts'));
    }

    public function addCart(Request $request)
    {
        // Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to add products to your cart!');
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found!');
        }

        $userId = Auth::id();

        // Periksa apakah produk sudah ada dalam keranjang pengguna
        $existingCartItem = Cart::where('user_id', $userId)->where('product_id', $request->product_id)->first();
        if ($existingCartItem) {
            // Jika produk sudah ada, tambahkan jumlahnya
            $existingCartItem->quantity += $request->quantity;
            $existingCartItem->save();
            return redirect()->back()->with('success', 'Product quantity updated in cart successfully!');
        }

        // Simpan item ke keranjang belanja
        Cart::create([
            'product_id' => $request->product_id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $request->quantity,
            'image' => $product->image,
            'user_id' => $userId,
        ]);

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function removeCart($id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return redirect()->back()->with('error', 'Cart item not found!');
        }

        $cart->delete();

        return redirect()->back()->with('success', 'Cart item removed successfully!');
    }

    public function productCount()
    {
        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->get();

        $cartCount = Cart::where('user_id', $userId)->sum('quantity');
        return view('back.pages.buyer.cart', compact('carts', 'cartCount'));
    }
}
