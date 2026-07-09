<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumno extends Model
{
    protected $primaryKey = 'user_id'; // Tu llave primaria personalizada
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 
        'dni', 
        'telefono', 
        'ciclo_id', 
        'carrera_id'
    ];

    // --- RELACIÓN FALTANTE ---
    public function matriculas(): HasMany 
    {
        // 'alumno_id' es la FK en la tabla matriculas
        // 'user_id' es la local key en la tabla alumnos
        return $this->hasMany(Matricula::class, 'alumno_id', 'user_id');
    }

    public function user(): BelongsTo { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function ciclo(): BelongsTo { 
        return $this->belongsTo(Ciclo::class); 
    }

    public function carrera(): BelongsTo { 
        return $this->belongsTo(Carrera::class); 
    }

    public function cursos(): HasMany {
        return $this->hasMany(Curso::class, 'ciclo_id', 'ciclo_id');
    }

    public function asistencias(): HasMany { 
        return $this->hasMany(Asistencia::class, 'alumno_id', 'user_id'); 
    }
}