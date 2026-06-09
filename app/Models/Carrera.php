<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nombre', 'area_id'])]
class Carrera extends Model
{
    use HasFactory;

    /**
     * Relación: La carrera pertenece a un área.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación: Una carrera tiene muchos alumnos postulando.
     */
    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class, 'carrera_id');
    }
}