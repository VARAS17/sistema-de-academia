<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\PagoMatricula;
use App\Models\ResultadoSimulacro;
use App\Models\Asistencia;
use App\Models\Area;
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

            $tablaAlumnos = $modeloAlumno->getTable();
            $pkAlumno = $modeloAlumno->getKeyName(); // Detecta si es 'id', 'alumno_id', etc.
            
            $tablaResultados = $modeloResultado->getTable();
            // Asumimos que la columna de puntaje se llama 'puntaje' y la foránea 'alumno_id'
            // Si tienen otros nombres, cámbiados abajo en el selectRaw

            // 2. Consulta de Rendimiento por Áreas
            $rendimiento_areas = Area::select('areas.nombre', 'areas.id')
                ->leftJoin('carreras', 'areas.id', '=', 'carreras.area_id')
                ->leftJoin($tablaAlumnos, 'carreras.id', '=', $tablaAlumnos . '.carrera_id')
                ->leftJoin($tablaResultados, $tablaAlumnos . '.' . $pkAlumno, '=', $tablaResultados . '.alumno_id')
                ->selectRaw("
                    COUNT(DISTINCT {$tablaAlumnos}.{$pkAlumno}) as total_alumnos, 
                    AVG({$tablaResultados}.puntaje) as promedio_area,
                    MAX({$tablaResultados}.puntaje) as puntaje_maximo
                ")
                ->groupBy('areas.id', 'areas.nombre')
                ->orderByDesc('promedio_area')
                ->get();

            $data = [
                'role' => 'admin',
                'total_alumnos' => Alumno::count(),
                'pagos_pendientes' => PagoMatricula::where('estado', 'pendiente')->count(),
                'monto_recaudado' => PagoMatricula::where('estado', 'pagado')->sum('monto'),
                'rendimiento_areas' => $rendimiento_areas,
            ];

        } else {
            // Lógica para ALUMNO
            $alumno = Alumno::where('user_id', $user->id)->first();

            if (!$alumno) {
                return view('dashboard', ['data' => ['role' => 'sin_perfil']]);
            }

            // Usamos la PK detectada también aquí por seguridad
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