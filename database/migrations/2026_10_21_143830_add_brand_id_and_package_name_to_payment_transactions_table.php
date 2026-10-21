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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('item_type')->default('product')->after('customer_phone');
            $table->foreignId('brand_id')->nullable()->after('product_id')->constrained('brands')->nullOnDelete();
            $table->string('package_name')->nullable()->after('item_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['item_type', 'brand_id', 'package_name']);
        });
    }
};
