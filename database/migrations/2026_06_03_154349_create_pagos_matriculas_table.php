<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_matricula', function (Blueprint $table) {
            $table->id();
            
            // Relación con la matrícula principal
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            
            // Información de la cuota
            $table->integer('numero_cuota'); // 1, 2 o 3
            $table->decimal('monto', 10, 2);
            $table->date('fecha_vencimiento');
            
            // Información del pago realizado
            $table->date('fecha_pago')->nullable();
            $table->string('evidencia')->nullable(); // Ruta del archivo/voucher
            
            // Estado del pago
            $table->enum('estado', ['Pendiente', 'Pagado', 'Observado'])->default('Pendiente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_matricula');
    }
};