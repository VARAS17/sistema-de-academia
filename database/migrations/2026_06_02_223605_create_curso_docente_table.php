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
        Schema::create('curso_docente', function (Blueprint $table) {
            $table->id();
            
            // Relación con Docente (usando user_id como referencia)
            $table->foreignId('docente_id')->constrained('docentes', 'user_id')->onDelete('cascade');
            
            // Relación con Curso
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_docente');
    }
};
