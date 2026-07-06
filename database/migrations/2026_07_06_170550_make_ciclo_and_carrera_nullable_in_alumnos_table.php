<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Permitir que ambos campos sean opcionales (nullables)
            $table->foreignId('ciclo_id')->nullable()->change();
            $table->foreignId('carrera_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->foreignId('ciclo_id')->nullable(false)->change();
            $table->foreignId('carrera_id')->nullable(false)->change();
        });
    }
};