<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Add this import

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) { // Correct table name
            DB::statement("ALTER TABLE purchase_orders MODIFY status ENUM('draft', 'pending', 'approved', 'rejected', 'ordered', 'received', 'cancelled') DEFAULT 'draft'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) { // Correct table name
            DB::statement("ALTER TABLE purchase_orders MODIFY status ENUM('draft', 'pending', 'approved', 'rejected', 'ordered', 'received', 'cancelled') DEFAULT 'draft'");
        });
    }
};