<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('guest')->get('/login', function () {
    return view('auth.login');
})->name('login');

Route::middleware('guest')->get('/login', function () {
    return view('auth.register');
})->name('register');

Route::get('/category', function () {
    return view('category.index');
})->name('category');

Route::get('/product', function () {
    return view('products.index');
})->name('product');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/checkout', [CartController::class, 'processCheckout'])->name('cart.processCheckout');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
// Registrasi dan verifikasi email
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register.create');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

Route::get('verify-email', [VerificationController::class, 'input'])->name('verification.input');
Route::post('verify-email', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('resend-verification', [VerificationController::class, 'resend'])->name('verification.resend');

// Rute yang dipanggil oleh Laravel Breeze untuk resend email verification
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// Middleware untuk pengguna yang login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute otentikasi default Laravel Breeze
require __DIR__ . '/auth.php';
