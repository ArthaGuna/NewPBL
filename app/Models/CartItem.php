<?php

// app/Models/CartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    // Field yang dapat diisi
    protected $fillable = [
        'cart_id',    // Menghubungkan CartItem ke Cart
        'product_id', // Menghubungkan ke produk
        'quantity',   // Jumlah barang
        'price'       // Harga per item
    ];

    // Relasi ke Cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
