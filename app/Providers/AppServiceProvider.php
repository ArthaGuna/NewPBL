<?php

namespace App\Providers;

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
    }
}
