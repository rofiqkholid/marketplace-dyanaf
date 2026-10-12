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
            $table->string('order_number')->unique(); // e.g. SEN100226-1-P29
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->enum('item_type', ['product', 'brand'])->default('product');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('item_name'); // Nama produk / brand
            $table->string('package_name')->nullable(); // Jika brand memiliki paket
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
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
