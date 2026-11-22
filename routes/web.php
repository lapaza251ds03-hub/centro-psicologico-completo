<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluacionClinicaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PsicologoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas públicas (Invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // RF01: Solicitud de acceso
    Route::get('/solicitar-acceso', [AuthController::class, 'mostrarSolicitud'])->name('solicitud.form');
    Route::post('/solicitar-acceso', [AuthController::class, 'registrarSolicitud'])->name('solicitud.store');
});

// Rutas protegidas (Personal con estado activo - RN01)
Route::middleware(['auth', 'rol'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // RF14 & RF15: Dashboard y Reportes
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reportes/citas-csv', [DashboardController::class, 'exportarCsv'])->name('reportes.csv');
    Route::get('/reportes/citas-pdf', [DashboardController::class, 'reporteImprimible'])->name('reportes.pdf');

    // RF02 & RF03: Gestión de Personal (Solo Administrador)
    Route::middleware('rol:admin')->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::post('/', [UsuarioController::class, 'store'])->name('store');
        Route::patch('/{usuario}/aprobar', [UsuarioController::class, 'aprobar'])->name('aprobar');
        Route::patch('/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('estado');
    });

    // RF04 - RF06: Pacientes (Todos los roles activos)
    Route::prefix('pacientes')->name('pacientes.')->group(function () {
        Route::get('/', [PacienteController::class, 'index'])->name('index');
        Route::post('/', [PacienteController::class, 'store'])->name('store');
        Route::get('/{paciente}/editar', [PacienteController::class, 'edit'])->name('edit');
        Route::put('/{paciente}', [PacienteController::class, 'update'])->name('update');
        Route::patch('/{paciente}/estado', [PacienteController::class, 'toggleEstado'])->name('estado');
    });

    // RF07: Equipo de Psicólogos (Consulta para todos; altas/bajas para Admin)
    Route::get('/psicologos', [PsicologoController::class, 'index'])->name('psicologos.index');
    Route::middleware('rol:admin')->group(function () {
        Route::post('/psicologos', [PsicologoController::class, 'store'])->name('psicologos.store');
        Route::patch('/psicologos/{psicologo}/estado', [PsicologoController::class, 'toggleEstado'])->name('psicologos.estado');
    });

    // RF08 - RF11: Agenda de Citas
    Route::prefix('citas')->name('citas.')->group(function () {
        Route::get('/', [CitaController::class, 'index'])->name('index');
        Route::post('/', [CitaController::class, 'store'])->name('store');
        Route::patch('/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('estado');
    });

    // RF12 - RF13 & RN09: Evaluaciones Clínicas Confidenciales (Solo Psicólogo y Admin)
    Route::middleware('rol:admin,psicologo')->prefix('evaluaciones')->name('evaluaciones.')->group(function () {
        Route::get('/cita/{cita}/crear', [EvaluacionClinicaController::class, 'create'])->name('create');
        Route::post('/cita/{cita}', [EvaluacionClinicaController::class, 'store'])->name('store');
        Route::get('/paciente/{paciente}/historial', [EvaluacionClinicaController::class, 'historial'])->name('historial');
    });
});