<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class horario extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si el modelo se llama Horario y la tabla horarios)
    protected $table = 'horarios';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'area_id',
        'ciclo_id',
        'imagen',
    ];

    /**
     * Obtener el área asociada al horario.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Obtener el ciclo asociado al horario.
     */
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }
}