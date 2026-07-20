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
        Schema::create('docentes', function (Blueprint $table) {
            // Relación 1:1 con users. El user_id es la PK.
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
            
            // Datos específicos del docente
            $table->string('dni', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('especialidad', 100)->nullable(); // Ej: Matemáticas, Historia
            $table->date('fecha_contratacion');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
