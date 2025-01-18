<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'city',
        'post_code',
        'address',
        'quantity',
        'sub_total_amount',
        'grand_total_amount',
        'discount_amount',
        'status',
        'payment_status',
        'shipping_cost',
        'product_id',
        'couriers_id',
        'promo_code_id',
        'snap_token',
    ];

    public static function generateUniqueTrxId() 
    {
        $prefix = 'ORD-';
        do {
            $randomString = $prefix . mt_rand(100000000000, 999999999999); // GRBH-19
        } while (self::where('order_number', $randomString)->exists());

        return $randomString;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);  // Jika relasi banyak ke banyak
    }

    // Jika Anda hanya memilih satu produk, maka relasinya bisa seperti ini:
    public function product()
    {
        return $this->belongsTo(Product::class);  // Jika relasi satu ke satu
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function productSizes(): BelongsToMany
    {
    return $this->belongsToMany(ProductSize::class, 'order_product_size')
                ->withPivot('quantity', 'price')  // Menyimpan quantity dan price di pivot
                ->withTimestamps();
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function courier()
    {
        return $this->hasMany(Courier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
