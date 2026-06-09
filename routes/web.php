<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CRUD\Ciclo;
use App\Livewire\CRUD\Curso;
use App\Livewire\CRUD\Docente;
use App\Livewire\CRUD\Alumno;
use App\Livewire\CRUD\Carrera;
use App\Livewire\CRUD\Matricula;
use App\Livewire\CRUD\GestionPagos;
use App\Livewire\CRUD\Simulacros;
use App\Livewire\CRUD\Puntajesimulacro;
use App\Livewire\CRUD\Asistencia;
use App\Livewire\CRUD\Horarios;
use App\Http\Controllers\DashboardController;


Route::view('/', 'pages.auth.login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


    
    Route::get('ciclos', Ciclo::class)->name('CRUD.ciclos');
    Route::get('cursos', Curso::class)->name('CRUD.cursos');
    Route::get('docentes', Docente::class)->name('CRUD.docentes');
    Route::get('alumnos', Alumno::class)->name('CRUD.alumnos');
    Route::get('carreras', Carrera::class)->name('CRUD.carreras');
    Route::get('matriculas', Matricula::class)->name('CRUD.matriculas');
    Route::get('gestion-pagos', GestionPagos::class)->name('CRUD.gestion-pagos');
    Route::get('simulacros', Simulacros::class)->name('CRUD.simulacros');
    Route::get('puntajes-simulacro', Puntajesimulacro::class)->name('CRUD.puntajes-simulacro');
    Route::get('asistencias', Asistencia::class)->name('CRUD.asistencias');
    Route::get('horarios', Horarios::class)->name('CRUD.horarios');
});

require __DIR__.'/settings.php';
