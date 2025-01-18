<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Relasi ke tabel users
            $table->string('order_number');
            $table->string('city');
            $table->string('post_code');
            // $table->string('product_size');
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete(); // Relasi ke tabel users

            $table->text('address');

            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('sub_total_amount'); // Harga sebelum diskon
            $table->unsignedBigInteger('grand_total_amount'); // Harga setelah diskon
            $table->unsignedBigInteger('discount_amount')->nullable();

            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');

            $table->string('shipping_cost')->nullable();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promo_code_id')->nullable()->constrained()->cascadeOnDelete();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
