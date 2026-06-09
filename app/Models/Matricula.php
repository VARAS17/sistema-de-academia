<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['alumno_id', 'ciclo_id', 'monto_total', 'modalidad', 'estado'])]
class Matricula extends Model
{
    use HasFactory;

    /**
     * Relación: La matrícula pertenece a un alumno.
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id', 'user_id');
    }

    /**
     * Relación: La matrícula corresponde a un ciclo académico.
     */
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }

    /**
     * Relación: Una matrícula tiene uno o varios pagos (cuotas).
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(PagoMatricula::class, 'matricula_id');
    }
}
