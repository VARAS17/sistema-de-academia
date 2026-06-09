<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControlAsistencia extends Model
{
    use HasFactory;

    // Definimos la tabla porque el plural en inglés no coincidirá
    protected $table = 'controles_asistencia';

    protected $fillable = [
        'ciclo_id',
        'area_id',
        'user_id',
        'fecha',
        'turno',
        'estado',
    ];

    // Relación: El control pertenece a un ciclo
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class);
    }

    // Relación: El control pertenece a un área
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    // Relación: Quién registró este control (Usuario administrativo)
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Un control tiene muchas marcas de asistencia de alumnos
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'control_asistencia_id');
    }
}