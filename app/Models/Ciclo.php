<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ciclo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'area_id',
        'aula',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function controlesAsistencia(): HasMany
    {
        return $this->hasMany(ControlAsistencia::class, 'ciclo_id');
    }


    public function horarios()
    {
        return $this->hasMany(Horario::class, 'ciclo_id');
    }
}