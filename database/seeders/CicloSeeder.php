<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ciclo;
use App\Models\Area; // Asegúrate de tener el modelo Area creado

class CicloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ciclos = [
            [
                'nombre' => 'Paralelo CEPUNT',
                'area_nombre' => 'Area A', // Nombre para buscar el ID
                'aula' => 'Aula 1'
            ],
            [
                'nombre' => 'Paralelo CEPUNT',
                'area_nombre' => 'Area B',
                'aula' => 'Aula 2'
            ],
            [
                'nombre' => 'Anual',
                'area_nombre' => 'Area A',
                'aula' => 'Aula 3'
            ],
            [
                'nombre' => 'Anual',
                'area_nombre' => 'Area B',
                'aula' => 'Aula 4'
            ],
            [
                'nombre' => 'Semestral',
                'area_nombre' => 'Area C',
                'aula' => 'Aula 5'
            ],
            [
                'nombre' => 'Semestral',
                'area_nombre' => 'Area D',
                'aula' => 'Aula 6'
            ],
        ];

        foreach ($ciclos as $datos) {
            // Buscamos el área por nombre
            $area = Area::where('nombre', $datos['area_nombre'])->first();

            if ($area) {
                Ciclo::create([
                    'nombre'  => $datos['nombre'],
                    'area_id' => $area->id,
                    'aula'    => $datos['aula'],
                    'activo'  => true,
                ]);
            } else {
                $this->command->warn("No se encontró el área: {$datos['area_nombre']}. El ciclo {$datos['nombre']} no fue creado.");
            }
        }
    }
}