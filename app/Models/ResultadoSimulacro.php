<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['simulacro_id', 'alumno_id', 'correctas', 'incorrectas', 'blanco', 'puntaje', 'puesto'])]
class ResultadoSimulacro extends Model
{
    use HasFactory;

    protected $table = 'resultados_simulacros';

    protected function casts(): array
    {
        return [
            'puntaje' => 'decimal:2',
            'puesto' => 'integer'
        ];
    }

    /**
     * El resultado pertenece a un simulacro específico.
     */
    public function simulacro(): BelongsTo
    {
        return $this->belongsTo(Simulacro::class);
    }

    /**
     * El resultado pertenece a un alumno.
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id', 'user_id');
    }
}