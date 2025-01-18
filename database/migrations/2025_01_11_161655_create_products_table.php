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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('thumbnail');
            $table->text('about');
            $table->unsignedBigInteger('stock');
            // $table->boolean('is_popular');

            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // Relasi ke table Categories
            $table->foreignId('product_size_id')->constrained('product_sizes')->cascadeOnDelete();

            $table->softDeletes(); // Agar saat menghapus file tidak hilang dari Database

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};