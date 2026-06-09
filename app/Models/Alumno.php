<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumno extends Model
{
    // Definimos user_id como llave primaria manual
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 
        'dni', 
        'telefono', 
        'ciclo_id', 
        'carrera_id'
    ];

    public function user(): BelongsTo { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function ciclo(): BelongsTo { 
        return $this->belongsTo(Ciclo::class); 
    }

    public function carrera(): BelongsTo { 
        return $this->belongsTo(Carrera::class); 
    }

    public function asistencias(): HasMany { 
        return $this->hasMany(Asistencia::class, 'alumno_id', 'user_id'); 
    }
}