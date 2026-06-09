<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['nombre', 'area_id', 'ciclo_id', 'fecha', 'puntaje_maximo'])]
class Simulacro extends Model
{
    use HasFactory;

    // Casteo para que la fecha se maneje automáticamente como un objeto Carbon
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'puntaje_maximo' => 'decimal:2'
        ];
    }

    /**
     * Relación: El simulacro pertenece a un Área.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación: El simulacro está dirigido a un Ciclo.
     */
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class);
    }

    /**
     * Relación Futura: Un simulacro tendrá muchos resultados.
     * (La descomentaremos cuando creemos la tabla de resultados)
     */
    
    public function resultados(): HasMany
    {
        return $this->hasMany(ResultadoSimulacro::class, 'simulacro_id');
    }
    
}