<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Verano 2024", "Anual 2025"
            
            // Usamos el campo 'area_id' para relacionar cada ciclo con un área específica
            $table->foreignId('area_id')->constrained('areas')->onDelete('restrict');
            
            $table->string('aula'); // Ej: "Aula 101", "Pabellón B - 202"
            
            // Sugerencia de experto: Añade un estado para saber si el ciclo está vigente
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos');
    }
};
