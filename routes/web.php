<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas públicas (Invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // RF01: Solicitud de acceso de personal
    Route::get('/solicitar-acceso', [AuthController::class, 'mostrarSolicitud'])->name('solicitud.form');
    Route::post('/solicitar-acceso', [AuthController::class, 'registrarSolicitud'])->name('solicitud.store');
});

// Rutas protegidas (Personal con estado activo - RN01)
Route::middleware(['auth', 'rol'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // RF02 & RF03: Gestión de personal exclusiva para Administrador
    Route::middleware('rol:admin')->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::post('/', [UsuarioController::class, 'store'])->name('store');
        Route::patch('/{usuario}/aprobar', [UsuarioController::class, 'aprobar'])->name('aprobar');
        Route::patch('/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('estado');
    });
});