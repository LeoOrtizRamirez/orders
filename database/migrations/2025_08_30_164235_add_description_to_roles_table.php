<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campos a la tabla permissions
        Schema::table('permissions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('group')->nullable()->after('description');
            $table->boolean('is_system')->default(false)->after('group');
        });

        // Agregar campos a la tabla roles
        Schema::table('roles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->boolean('is_system')->default(false)->after('description');
            $table->boolean('is_default')->default(false)->after('is_system');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'group', 'is_system']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_system', 'is_default']);
        });
    }
};