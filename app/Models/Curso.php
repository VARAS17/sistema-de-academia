<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curso extends Model
{
    protected $fillable = ['nombre', 'ciclo_id', 'area_id'];

    /**
     * Relación: Un curso pertenece a un área.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación: Un curso pertenece a un ciclo.
     */
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class);
    }

    /**
     * Relación: Un curso puede ser dictado por muchos docentes.
     */
    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(Docente::class, 'curso_docente', 'curso_id', 'docente_id')
                    ->withTimestamps();
    }
}