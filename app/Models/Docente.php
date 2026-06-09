<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['user_id', 'dni', 'telefono', 'especialidad', 'fecha_contratacion'])]
class Docente extends Model
{
    use HasFactory;

    // Indicamos que la PK es user_id y no es autoincremental
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * Casting de datos para manejar fechas como objetos Carbon
     */
    protected function casts(): array
    {
        return [
            'fecha_contratacion' => 'date',
        ];
    }

    /**
     * RELACIÓN CRÍTICA: Conecta el perfil con la identidad (User)
     * Debe llamarse 'user' (en singular)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación Muchos a Muchos: Un docente dicta varios cursos
     */
    public function cursos(): BelongsToMany
    {
        // docente_id es la FK en la tabla pivot que apunta a docentes.user_id
        return $this->belongsToMany(Curso::class, 'curso_docente', 'docente_id', 'curso_id')
                    ->withTimestamps();
    }
}