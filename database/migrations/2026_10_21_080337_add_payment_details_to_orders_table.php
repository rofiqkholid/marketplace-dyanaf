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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('midtrans_transaction_id')->nullable()->after('snap_token');
            $table->string('bank')->nullable()->after('payment_type');
            $table->string('va_number')->nullable()->after('bank');
            $table->text('qris_url')->nullable()->after('va_number');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['midtrans_transaction_id', 'bank', 'va_number', 'qris_url', 'paid_at']);
        });
    }
};
