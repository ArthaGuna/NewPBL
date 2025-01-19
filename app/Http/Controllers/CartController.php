<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    
    public function index()
    {
        // Ambil cart berdasarkan user yang sedang login
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        // Ambil semua item dalam keranjang
        $cartItems = $cart->items()
            ->with(['product.photos'])
            ->get();

        // Hitung jumlah produk unik berdasarkan cartItems-id (bukan berdasarkan quantity)
        $totalItems = $cartItems->unique('product_id')->count();  // Menghitung jumlah produk unik berdasarkan ID

        // Kirim data ke view
        return view('cart.index', compact('cartItems', 'totalItems'));  // Pastikan totalItems disertakan
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'size' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
            ]);

            // Cek apakah produk ada
            $product = Product::findOrFail($validated['product_id']);
            
            // Dapatkan atau buat cart
            $cart = Cart::firstOrCreate([
                'user_id' => auth()->id()
            ]);

            // Cek dan update atau buat item cart
            $cartItem = CartItem::updateOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_id' => $validated['product_id'],
                    'size' => $validated['size']
                ],
                [
                    'quantity' => $validated['quantity'],
                    'price' => $validated['price']
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart_item' => $cartItem->load('product')
            ]);

        } catch (\Exception $e) {
            Log::error('Cart store error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan produk ke keranjang'
            ], 500);  // Return 500 Internal Server Error
        }
    }
 
    public function update(Request $request, $id)
{
    $cartItem = CartItem::find($id);
    
    if (!$cartItem) {
        return response()->json(['status' => 'error', 'message' => 'Item tidak ditemukan'], 404);
    }

    $quantity = $request->input('quantity');
    
    if ($quantity < 1) {
        return response()->json(['status' => 'error', 'message' => 'Jumlah tidak boleh kurang dari 1'], 400);
    }

    $cartItem->quantity = $quantity;
    $cartItem->subtotal = $cartItem->product->price * $quantity;
    $cartItem->save();

    // Mengembalikan response dengan status sukses
    return response()->json([
        'status' => 'success',
        'message' => 'Item diperbarui',
        'subtotal' => $cartItem->subtotal
    ]);
}

    
        public function destroy($id)
        {
            try {
                // Temukan item berdasarkan ID
                $cartItem = CartItem::findOrFail($id);
    
                // Hapus item dari keranjang
                $cartItem->delete();
    
                return response()->json(['status' => 'success', 'message' => 'Item berhasil dihapus dari keranjang']);
            } catch (\Exception $e) {
                Log::error('Cart delete error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus item dari keranjang'], 500);
            }
        }

}
