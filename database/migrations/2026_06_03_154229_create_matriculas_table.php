<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            
            // Relación con el alumno (recordando que su PK es user_id)
            $table->foreignId('alumno_id')->constrained('alumnos', 'user_id')->onDelete('restrict');
            
            // Relación con el ciclo al que se matricula
            $table->foreignId('ciclo_id')->constrained('ciclos')->onDelete('restrict');
            
            // Monto total pactado para este ciclo
            $table->decimal('monto_total', 10, 2);
            
            // Modalidad de pago seleccionada
            $table->enum('modalidad', ['Pago Unico', '2 Cuotas', '3 Cuotas']);
            
            // Estado de la matrícula
            $table->enum('estado', ['Pendiente', 'Activa', 'Anulada'])->default('Pendiente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};