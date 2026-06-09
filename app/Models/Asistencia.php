<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'control_asistencia_id',
        'alumno_id',
        'estado',
        'hora_marcado',
        'observacion',
    ];

    // Relación: La marca pertenece a un control (cabecera)
    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlAsistencia::class, 'control_asistencia_id');
    }

    // Relación: La marca pertenece a un alumno
    // Nota: 'alumno_id' apunta a 'user_id' en la tabla alumnos
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id', 'user_id');
    }
}