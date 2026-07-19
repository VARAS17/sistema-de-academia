<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\PagoMatricula;
use App\Models\ResultadoSimulacro;
use App\Models\Asistencia;
use App\Models\Area;
use App\Models\Matricula;
use App\Models\Ciclo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            
            // 1. Detectar nombres de tablas y llaves primarias dinámicamente
            $modeloAlumno = new Alumno();
            $modeloResultado = new ResultadoSimulacro();
            $modeloMatricula = new Matricula();

            $tablaAlumnos = $modeloAlumno->getTable();
            $pkAlumno = $modeloAlumno->getKeyName(); // 'user_id'
            
            $tablaResultados = $modeloResultado->getTable();
            $tablaMatriculas = $modeloMatricula->getTable();

            // 2. Rendimiento por Áreas (vía matriculas, ya que alumnos.carrera_id quedó obsoleto)
            $rendimiento_areas = Area::select('areas.nombre', 'areas.id')
                ->leftJoin('carreras', 'areas.id', '=', 'carreras.area_id')
                ->leftJoin($tablaMatriculas, 'carreras.id', '=', $tablaMatriculas . '.carrera_id')
                ->leftJoin($tablaAlumnos, $tablaMatriculas . '.alumno_id', '=', $tablaAlumnos . '.' . $pkAlumno)
                ->leftJoin($tablaResultados, $tablaAlumnos . '.' . $pkAlumno, '=', $tablaResultados . '.alumno_id')
                ->selectRaw("
                    COUNT(DISTINCT {$tablaAlumnos}.{$pkAlumno}) as total_alumnos, 
                    AVG({$tablaResultados}.puntaje) as promedio_area,
                    MAX({$tablaResultados}.puntaje) as puntaje_maximo
                ")
                ->groupBy('areas.id', 'areas.nombre')
                ->orderByDesc('promedio_area')
                ->get();

            // 3. Alumnos por Ciclo (todos los ciclos, activos e inactivos)
            // 3. Alumnos por Ciclo (Corregido para buscar en la tabla matriculas)
            $alumnos_por_ciclo = Ciclo::select('ciclos.id', 'ciclos.nombre', 'areas.nombre as area_nombre')
                ->leftJoin('areas', 'ciclos.area_id', '=', 'areas.id')
                // Unimos con matrículas en lugar de alumnos directamente
                ->leftJoin($tablaMatriculas, 'ciclos.id', '=', $tablaMatriculas . '.ciclo_id')
                // Unimos con alumnos para contar los registros reales
                ->leftJoin($tablaAlumnos, $tablaMatriculas . '.alumno_id', '=', $tablaAlumnos . '.' . $pkAlumno)
                ->selectRaw("COUNT(DISTINCT {$tablaMatriculas}.alumno_id) as total_alumnos")
                ->groupBy('ciclos.id', 'ciclos.nombre', 'areas.nombre')
                ->orderBy('ciclos.nombre')
                ->get();
            $data = [
                'role' => 'admin',
                'total_alumnos' => Alumno::count(),
                'pagos_pendientes' => PagoMatricula::where('estado', 'pendiente')->count(),
                'monto_recaudado' => PagoMatricula::where('estado', 'pagado')->sum('monto'),
                'rendimiento_areas' => $rendimiento_areas,
                'alumnos_por_ciclo' => $alumnos_por_ciclo,
            ];

        } else {
            // Lógica para ALUMNO
            $alumno = Alumno::with('matriculaActiva.carrera')->where('user_id', $user->id)->first();

            if (!$alumno) {
                return view('dashboard', ['data' => ['role' => 'sin_perfil']]);
            }

            $pk = $alumno->getKeyName();

            $data = [
                'role' => 'alumno',
                'alumno' => $alumno,
                'mis_pagos' => PagoMatricula::whereHas('matricula', function($q) use ($alumno, $pk) {
                    $q->where('alumno_id', $alumno->$pk);
                })->get(),
                'proximo_pago' => PagoMatricula::whereHas('matricula', function($q) use ($alumno, $pk) {
                        $q->where('alumno_id', $alumno->$pk);
                    })
                    ->where('estado', 'pendiente')
                    ->orderBy('fecha_vencimiento', 'asc')
                    ->first(),
                'ultimo_resultado' => ResultadoSimulacro::where('alumno_id', $alumno->$pk)
                    ->with('simulacro')
                    ->latest()
                    ->first(),
                'porcentaje_asistencia' => $this->calcularAsistencia($alumno->$pk),
                'mis_resultados' => ResultadoSimulacro::where('alumno_id', $alumno->$pk)
                    ->with('simulacro')
                    ->latest()
                    ->take(5)
                    ->get(),
            ];
        }

        return view('dashboard', compact('data'));
    }

    private function calcularAsistencia($alumno_id)
    {
        $total = Asistencia::where('alumno_id', $alumno_id)->count();
        if ($total == 0) return 0;
        $presente = Asistencia::where('alumno_id', $alumno_id)->where('estado', 'presente')->count();
        return round(($presente / $total) * 100, 2);
    }
}