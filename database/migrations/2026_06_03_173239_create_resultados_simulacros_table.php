<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultados_simulacros', function (Blueprint $table) {
            $table->id();
            
            // Relación con el simulacro
            $table->foreignId('simulacro_id')->constrained('simulacros')->onDelete('cascade');
            
            // Relación con el alumno (PK es user_id)
            $table->foreignId('alumno_id')->constrained('alumnos', 'user_id')->onDelete('cascade');
            
            // Detalle de respuestas (Opcional, pero recomendado para el cálculo)
            $table->integer('correctas')->default(0);
            $table->integer('incorrectas')->default(0);
            $table->integer('blanco')->default(0);
            
            // Puntaje final obtenido
            $table->decimal('puntaje', 8, 2);
            
            // Puesto en el ranking (Se puede calcular y guardar aquí)
            $table->integer('puesto')->nullable();

            // Un alumno solo puede tener un resultado por simulacro
            $table->unique(['simulacro_id', 'alumno_id']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados_simulacros');
    }
};