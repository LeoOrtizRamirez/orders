<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->text('note');
            $table->boolean('is_important')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('is_important');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notes');
    }
};