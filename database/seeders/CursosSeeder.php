<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ciclo;
use App\Models\Curso; // Asegúrate de tener el modelo Curso

class CursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definimos la lista de cursos comunes
        $nombresCursos = [
            'Desarrollo Personal',
            'Ciudadanía y Civica',
            'Historia',
            'Geografía',
            'Economia',
            'Comunicologia',
            'Analisis del Discurso',
            'Literatura',
            'Matematica',
            'Biologia',
            'Fisica',
            'Quimica',
        ];

        // 2. Obtenemos todos los ciclos creados por el CicloSeeder
        $ciclos = Ciclo::all();

        if ($ciclos->isEmpty()) {
            $this->command->warn("No hay ciclos encontrados. Ejecuta primero CicloSeeder.");
            return;
        }

        // 3. Iteramos por cada ciclo y le asignamos todos los cursos
        foreach ($ciclos as $ciclo) {
            foreach ($nombresCursos as $nombre) {
                // Creamos el curso pasando tanto el ciclo_id como el area_id
                Curso::firstOrCreate([
                    'nombre'   => $nombre,
                    'ciclo_id' => $ciclo->id,
                    'area_id'  => $ciclo->area_id, // <--- Tomamos el area_id del ciclo actual
                ]);
            }
        }

        $this->command->info("Cursos asignados correctamente a todos los ciclos.");
    }
}
