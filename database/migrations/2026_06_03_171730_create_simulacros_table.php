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
        Schema::create('simulacros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "1er Simulacro Tipo Admisión"
            
            // Relación con el área (Define el tipo de examen)
            $table->foreignId('area_id')->constrained('areas')->onDelete('restrict');
            
            // Relación con el ciclo (Define a qué grupo de alumnos va dirigido)
            $table->foreignId('ciclo_id')->constrained('ciclos')->onDelete('restrict');
            
            $table->date('fecha');
            
            // Puntaje máximo (Útil para validaciones de notas y porcentajes)
            $table->decimal('puntaje_maximo', 8, 2)->default(1000.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulacros');
    }
};