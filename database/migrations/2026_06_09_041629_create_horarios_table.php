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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Horario Mañana - Ciclo Verano"

            // Relación con el área
            $table->foreignId('area_id')->constrained('areas')->onDelete('restrict');

            // Relación con el ciclo 
            // (La lógica de filtrar ciclos por área se maneja en el Frontend/Controlador)
            $table->foreignId('ciclo_id')->constrained('ciclos')->onDelete('restrict');

            // Ruta de la imagen del horario
            $table->string('imagen')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};