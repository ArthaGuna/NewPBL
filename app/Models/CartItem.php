<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',   
        'product_id',
        'size',
        'quantity',  
        'price'      
    ];

        // Relasi ke Cart
        public function cart()
        {
            return $this->belongsTo(Cart::class);
        }

        // Relasi ke Product
        public function product()
        {
            return $this->belongsTo(Product::class);
        }

        public function getSubtotalAttribute()
        {
            return $this->quantity * $this->price;
        }
    }
