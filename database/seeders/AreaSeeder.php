<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['nombre' => 'Area A', 'descripcion' => 'Ciencias de la Salud'],
            ['nombre' => 'Area B', 'descripcion' => 'Ciencias Básicas'],
            ['nombre' => 'Area C', 'descripcion' => 'Ingenierías'],
            ['nombre' => 'Area D', 'descripcion' => 'Económicas y Letras'],
        ];

        foreach ($areas as $area) {
            DB::table('areas')->updateOrInsert(['nombre' => $area['nombre']], $area);
        }
    }
}
