<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo_path',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
