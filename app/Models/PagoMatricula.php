<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['matricula_id', 'numero_cuota', 'monto', 'fecha_vencimiento', 'fecha_pago', 'evidencia', 'estado'])]
class PagoMatricula extends Model
{
    use HasFactory;

    // Indicamos explícitamente el nombre de la tabla
    protected $table = 'pagos_matricula';

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
            'fecha_pago' => 'date',
            'monto' => 'decimal:2'
        ];
    }

    /**
     * Relación: El pago pertenece a una matrícula.
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class, 'matricula_id');
    }
}