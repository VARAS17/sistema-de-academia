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
            Schema::create('alumnos', function (Blueprint $table) {
                $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
                $table->string('dni', 20)->unique();
                $table->string('telefono', 20)->nullable();
                
                $table->foreignId('ciclo_id')->constrained('ciclos')->onDelete('restrict');
                $table->foreignId('carrera_id')->constrained('carreras')->onDelete('restrict');
                
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
