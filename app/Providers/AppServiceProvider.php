<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $appName = 'Amerta Sedana';

        $customTitles = [
            'home' => 'Beranda',
            'about' => 'Tentang Kami',
            'products' => 'Produk',
            'category' => 'Kategori',
            'login' => 'Masuk',
            'register' => 'Daftar'
        ];

        // Gunakan view composer untuk memastikan route name tersedia saat view dirender
        View::composer('*', function ($view) use ($appName, $customTitles) {
            $routeName = Route::currentRouteName();

            if ($routeName && isset($customTitles[$routeName])) {
                $title = $customTitles[$routeName] . " - " . $appName;
            } else {
                $title = $appName; // Default jika nama rute tidak ditemukan
            }

            Log::info("Route Name: " . ($routeName ?? 'null'));
            Log::info("Title: $title");

            $view->with('title', $title);
        });

        View::composer('*', function ($view) {
            // Mengecek apakah pengguna sedang login
            if (auth()->check()) {
                // Ambil keranjang berdasarkan user yang sedang login
                $cart = Cart::where('user_id', auth()->id())->first();
    
                if ($cart) {
                    // Hitung jumlah item berdasarkan ID unik (cartItems-id) di keranjang
                    $totalItems = $cart->items->unique('product_id')->count(); // Menghitung jumlah ID unik
                } else {
                    $totalItems = 0;
                }
    
                // Kirimkan variabel totalItems ke semua view
                $view->with('totalItems', $totalItems);
            } else {
                // Jika pengguna tidak login, kirimkan totalItems = 0
                $view->with('totalItems', 0);
            }
        });
    }
}
