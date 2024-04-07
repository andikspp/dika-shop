<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function create()
    {
        return view('back.pages.buyer.product');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Menambahkan validasi untuk jenis dan ukuran gambar
        ]);

        // Mengunggah gambar ke penyimpanan lokal (storage/app/public)
        // $imagePath = $request->file('image')->store('img', 'public');

        $gambar = $request->file('image');
        $extension = $gambar->getClientOriginalExtension();
        Storage::disk('public')->put($gambar->getFilename() . '.' . $extension,  File::get($gambar));

        // Membuat produk baru dengan menggunakan data yang disertakan dalam request
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category' => $request->category,
            'image' => $gambar->getFilename() . '.' . $extension,
            // 'image' => $imagePath, // Menyimpan path gambar ke database
        ]);

        return redirect()->route('products.create')->with('success', 'Product added successfully!');
    }

    public function showObat()
    {
        $produkObat = Product::where('category', 'Obat Herbal')->get();
        return view('product.obat', compact('produkObat'));
    }

    public function showSabun()
    {
        $produkSabun = Product::where('category', 'Sabun Cuci')->get();
        return view('product.sabun', compact('produkSabun'));
    }

    public function showFood()
    {
        $produkFood = Product::where('category', 'Makanan & Minuman')->get();
        return view('product.food', compact('produkFood'));
    }

    public function showDetail($id)
    {
        $product = Product::findOrFail($id);
        return view('back.pages.buyer.detail-product', compact('product'));
    }

    public function searchByName(Request $request)
    {
        $query = $request->input('query');
        $product = Product::where('name', 'like', '%' . $query . '%')->first();
        if ($product) {
            return redirect()->route('products.show', ['id' => $product->id]);
        } else {
            return redirect()->back()->with('error', 'Product not found!');
        }
    }

    public function cartPage()
    {
        return view('back.pages.buyer.cart');
    }
}
