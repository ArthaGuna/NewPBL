<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Courier;
use App\Models\PromoCode;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Menampilkan keranjang belanja
    public function index()
{
    // Mengambil data cart yang relevan untuk user yang sedang login
    $cart = Cart::where('user_id', auth()->id())->first();

    $totalItems = CartItem::whereHas('cart', function ($query) {
        $query->where('user_id', auth()->id());
    })->sum('quantity');

    // Jika cart tidak ditemukan atau tidak ada item di dalam cart
    if (!$cart || $cart->items->isEmpty()) {
        return view('cart.index')->with('error', 'Keranjang Anda kosong.');
    }

    // Menghitung subtotal
    $subTotal = 0;
    foreach ($cart->items as $item) {
        $subTotal += $item->quantity * $item->price;
    }

    // Mengirim data ke view
    return view('cart.index', compact('cart', 'subTotal', 'totalItems'));
}


public function addToCart(Request $request)
{
    // Ambil data pengguna dan produk
    $user = auth()->user();
    $product = Product::findOrFail($request->product_id);
    
    // Cek apakah keranjang sudah ada untuk user
    $cart = Cart::firstOrCreate(['user_id' => $user->id]);
    
    // Cek apakah produk sudah ada di dalam keranjang
    $cartItem = CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $product->id)
                        ->first();

    if ($cartItem) {
        // Jika produk sudah ada, update quantity
        $cartItem->update([
            'quantity' => $cartItem->quantity + $request->quantity
        ]);
    } else {
        // Jika produk belum ada, buat item baru di keranjang
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->price // Pastikan harga ditambahkan
        ]);
    }

    // Setelah berhasil menambahkan, redirect ke halaman sebelumnya dengan notifikasi
    return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
}



    // Menampilkan halaman checkout
    public function checkout()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $subTotalAmount = 0;
        foreach ($cart->items as $item) {
            $subTotalAmount += $item->quantity * $item->price;
        }

        $couriers = Courier::where('is_active', true)->get();
        return view('cart.checkout', compact('cart', 'subTotalAmount', 'couriers'));
    }

    // Memproses checkout dan menyimpan data ke tabel orders
    public function processCheckout(Request $request)
    {
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $subTotalAmount = 0;
        $quantity = 0;
        $discountAmount = 0;

        // Kalkulasi sub total dan quantity
        foreach ($cart->items as $item) {
            $subTotalAmount += $item->quantity * $item->price;
            $quantity += $item->quantity;
        }

        // Cek apakah ada kode promo
        $promo = null;
        if ($request->promo_code) {
            $promo = PromoCode::where('code', $request->promo_code)->first();
            if ($promo) {
                $discountAmount = $promo->discount_amount;
            }
        }

        $grandTotalAmount = $subTotalAmount - $discountAmount;

        // Simpan data ke tabel orders
        $orderNumber = 'ORD' . strtoupper(bin2hex(random_bytes(12))); // Contoh pembuatan nomor order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'address' => $request->address,
            'courier_id' => $request->courier_id,
            'quantity' => $quantity,
            'sub_total_amount' => $subTotalAmount,
            'grand_total_amount' => $grandTotalAmount,
            'discount_amount' => $discountAmount,
            'shipping_cost' => '0', // Ditentukan berdasarkan kurir
            'promo_code_id' => $promo ? $promo->id : null
        ]);

        // Pindahkan data cart ke tabel orders dan order_items
        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price
            ]);
        }

        // Hapus cart setelah checkout
        $cart->items()->delete();
        $cart->delete();

        return redirect()->route('orders.show', $order->id)->with('success', 'Checkout berhasil.');
    }
}
