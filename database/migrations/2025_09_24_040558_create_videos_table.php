<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Título del vídeo
            $table->string('slug')->unique(); // URL amigable
            $table->text('description')->nullable(); // Descripción
            $table->string('video_url'); // URL del vídeo (YouTube, Vimeo, etc.)
            $table->string('thumbnail_url')->nullable(); // Miniatura
            $table->integer('duration_seconds')->default(0); // Duración en segundos
            $table->integer('order')->default(0); // Orden de reproducción
            $table->boolean('is_free')->default(false); // ¿Gratuito?
            $table->boolean('is_active')->default(true); // Activo/inactivo
            $table->json('tags')->nullable(); // Etiquetas
            $table->timestamps();
            
            $table->index(['service_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('videos');
    }
}