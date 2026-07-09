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
        Schema::table('alumnos', function (Blueprint $table) {
            // Opción A: Hacerlo opcional
            $table->foreignId('ciclo_id')->nullable()->change();
            
            // Opción B: Eliminar la columna si ya no la necesitas ahí
            // $table->dropForeign(['ciclo_id']); // Primero quita la llave foránea
            // $table->dropColumn('ciclo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            //
        });
    }
};
