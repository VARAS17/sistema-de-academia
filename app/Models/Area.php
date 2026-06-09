<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['nombre', 'descripcion'])]
class Area extends Model
{
    /**
     * Relación directa: Una área tiene muchos ciclos.
     * Ejemplo: Area A -> Ciclo Verano, Ciclo Anual.
     */
    public function ciclos(): HasMany
    {
        return $this->hasMany(Ciclo::class, 'area_id');
    }

    /**
     * Relación indirecta: Una área tiene muchos cursos A TRAVÉS de los ciclos.
     * Esto es muy útil para reportes.
     * Ejemplo: $area->cursos
     */
    public function cursos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Curso::class, // Modelo de destino
            Ciclo::class, // Modelo intermedio
            'area_id',    // Llave foránea en Ciclo
            'ciclo_id',   // Llave foránea en Curso
            'id',         // Llave local en Area
            'id'          // Llave local en Ciclo
        );
    }

    /**
     * Relación directa: Una área tiene muchas carreras.
     */
    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class, 'area_id');
    }

    public function controlesAsistencia(): HasMany
    {
        return $this->hasMany(ControlAsistencia::class, 'area_id');
    }
    

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'area_id');
    }
}