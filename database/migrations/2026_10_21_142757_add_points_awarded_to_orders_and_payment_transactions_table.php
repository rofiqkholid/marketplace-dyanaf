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
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'points_awarded')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('points_awarded')->default(false)->after('paid_at');
            });
        }

        if (Schema::hasTable('payment_transactions') && !Schema::hasColumn('payment_transactions', 'points_awarded')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->boolean('points_awarded')->default(false)->after('paid_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'points_awarded')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('points_awarded');
            });
        }

        if (Schema::hasTable('payment_transactions') && Schema::hasColumn('payment_transactions', 'points_awarded')) {
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->dropColumn('points_awarded');
            });
        }
    }
};
