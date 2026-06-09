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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();

            // Relación con la cabecera (El control por área/fecha/turno)
            $table->foreignId('control_asistencia_id')
                  ->constrained('controles_asistencia')
                  ->onDelete('cascade');

            // Relación con el alumno (user_id es la PK en tu tabla alumnos)
            $table->foreignId('alumno_id')
                  ->constrained('alumnos', 'user_id')
                  ->onDelete('cascade');

            // Estados simplificados según tu requerimiento
            $table->enum('estado', ['presente', 'tardanza', 'falta'])
                  ->default('falta');

            // Registro de la hora para control de puntualidad
            $table->time('hora_marcado')->nullable();

            // Espacio para notas breves
            $table->string('observacion')->nullable();

            // Evita duplicados: Un alumno solo tiene un estado por cada control
            $table->unique(['control_asistencia_id', 'alumno_id'], 'idx_alumno_control_unico');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};