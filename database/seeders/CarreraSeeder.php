<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        // Función auxiliar para obtener el ID o fallar con un mensaje claro
        $getAreaId = function($nombre) {
            $area = DB::table('areas')->where('nombre', $nombre)->first();
            if (!$area) {
                throw new \Exception("Error: No se encontró el '$nombre'. Asegúrate de ejecutar AreaSeeder primero.");
            }
            return $area->id;
        };

        $carreras = [
            //AREA A
            ['nombre' => 'Ciencias Biologicas', 'area_id' => $getAreaId('Area A')],
            ['nombre' => 'Enfermería', 'area_id' => $getAreaId('Area A')],
            ['nombre' => 'Farmacia y Bioquímica', 'area_id' => $getAreaId('Area A')],
            ['nombre' => 'Medicina', 'area_id' => $getAreaId('Area A')],
            ['nombre' => 'Microbiología  y Parasitología', 'area_id' => $getAreaId('Area A')],
            ['nombre' => 'Estomatología', 'area_id' => $getAreaId('Area A')],
            ['nombre' => 'Biologia Pesquera', 'area_id' => $getAreaId('Area A')],
            
            //AREA B
            ['nombre' => 'Estadistica', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Fisica', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingeniería Industrial', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Mecanica', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Metalurgica', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Quimica', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Matematicas', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Zootecnista', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Agricola', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Agroindustrial', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Informatica', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Agronomia', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria de Sistemas', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria de Minas', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria de Materiales', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Ambiental', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Mecatronica', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Ingenieria Civil', 'area_id' => $getAreaId('Area B')],
            ['nombre' => 'Arquitectura y Urbanismo', 'area_id' => $getAreaId('Area B')],
            

            //AREA C
            ['nombre' => 'Antropologia', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Arqueologia', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Derecho', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Trabajo Social', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Turismo', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Inicial', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Primaria', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Ciencias de la Comunicacion', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Historia', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Secundaria: Filosofia, psicologia y ciencias sociales', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Secundaria: Ciencias Matematicas', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Secundaria: Ciencias Naturales, fisica, quimica y biologia', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Secundaria: Lenguaje y Literatura', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Secundaria: Idiomas', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Educacion Secundaria: Historia y Geografia', 'area_id' => $getAreaId('Area C')],
            ['nombre' => 'Ciencias Politica y gobernabilidad', 'area_id' => $getAreaId('Area C')],


            //AREA D
            ['nombre' => 'Adminstracion', 'area_id' => $getAreaId('Area D')],
            ['nombre' => 'Contabilidad y Finanzas', 'area_id' => $getAreaId('Area D')],
            ['nombre' => 'Economia', 'area_id' => $getAreaId('Area D')],
        ];

        foreach ($carreras as $carrera) {
            DB::table('carreras')->updateOrInsert(['nombre' => $carrera['nombre']], $carrera);
        }
    }
}