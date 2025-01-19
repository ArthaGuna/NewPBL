<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', // patung budha duduk
        'slug', // untuk menampilkan url detail produk : domain.com/patung-budha-duduk
        'thumbnail',
        'about',
        'stock',
        // 'is_popular',
        'category_id', // foreign key
    ];
    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value); // untuk generate url secara otomatis 
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function photos(): HasMany 
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function sizes(): HasMany 
    {
        return $this->hasMany(ProductSize::class);
    }
}
