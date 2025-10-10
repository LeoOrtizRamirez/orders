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
        Schema::create('profile_metrics', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->integer('visitas_perfil')->default(0);
            $table->decimal('ganancias_suscripciones', 10, 2)->default(0);
            $table->integer('nuevas_suscripciones')->default(0);
            $table->integer('renovaciones')->default(0);
            
            $table->dateTime('fecha_registro');
            
            $table->timestamps();
            
            $table->index(['user_id', 'fecha_registro']);
            $table->index('fecha_registro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_metrics');
    }
};