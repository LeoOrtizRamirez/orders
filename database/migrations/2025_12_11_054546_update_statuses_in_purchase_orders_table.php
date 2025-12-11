<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateStatusesInPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('nuevo pedido', 'disponibilidad', 'preparar pedido', 'en preparación', 'facturación', 'en despacho', 'en ruta', 'entregado') NOT NULL DEFAULT 'nuevo pedido'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected', 'ordered', 'received', 'cancelled') NOT NULL DEFAULT 'draft'");
    }
};
