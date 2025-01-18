<?php

namespace App\Http\Controllers;

use App\Models\Product; // Pastikan modelnya sesuai
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Ambil semua data produk dari database
        $products = Product::with('photos', 'category', 'sizes')->paginate(3);

        // Kirim data produk ke view
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        // Ambil produk berdasarkan ID
        $product = Product::with('photos', 'category', 'sizes')->findOrFail($id);

        // Kirim data produk ke view
        return view('products.show', compact('product'));
    }

}
