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
        Schema::create('controles_asistencia', function (Blueprint $table) {
            $table->id();
            
            // Relación con el ciclo (ej. Verano 2026, Anual, etc.)
            $table->foreignId('ciclo_id')->constrained('ciclos')->onDelete('restrict');
            
            // Relación con el Área (ej. Área A, Área B, etc.)
            // Asumiendo que tienes una tabla 'areas'
            $table->foreignId('area_id')->constrained('areas')->onDelete('restrict');
            
            // Quién registra la asistencia
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            $table->date('fecha');
            $table->enum('turno', ['mañana', 'tarde', 'noche']);
            
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');

            // Unicidad: Solo un control por Ciclo, Área, Fecha y Turno
            $table->unique(['ciclo_id', 'area_id', 'fecha', 'turno'], 'idx_control_area_unico');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controles_asistencia');
    }
};