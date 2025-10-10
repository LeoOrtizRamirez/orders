<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del servicio
            $table->string('slug')->unique(); // URL amigable
            $table->text('description')->nullable(); // Descripción
            $table->integer('price')->default(0); // Precio
            $table->integer('duration_minutes')->default(60); // Duración en minutos
            $table->boolean('is_active')->default(true); // Activo/inactivo
            $table->integer('order')->default(0); // Orden de visualización
            $table->string('icon')->nullable(); // Ícono del servicio
            $table->string('color')->nullable(); // Color representativo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}