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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('quantity', 12, 2)->change();
            $table->decimal('received_quantity', 12, 2)->default(0)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 12, 2)->change();
            $table->decimal('min_stock', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('received_quantity')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->change();
            $table->integer('min_stock')->change();
        });
    }
};