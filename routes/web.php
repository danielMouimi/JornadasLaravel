<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PonenteController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\AsistenciaController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// 🔹 Rutas de autenticación (Generadas por Laravel Breeze)
require __DIR__.'/auth.php';

// 🔹 Rutas accesibles solo para usuarios autenticados y verificados (excepto pago)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pago', [PagoController::class, 'index'])->name('pago.index');
    Route::post('/pago/procesar', [PagoController::class, 'procesarPago'])->name('pago.procesar');
});

// 🔹 Rutas accesibles solo para usuarios autenticados, verificados y con registro completo
Route::middleware(['auth', 'verified', 'registro.completo'])->group(function () {
    // Dashboard del usuario
    Route::get('/dashboard1', [DashboardController::class, 'index'])->name('dashboard');

    // Ponentes
    Route::get('/ponentes', [PonenteController::class, 'index'])->name('ponentes.index');


    // Eventos
    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
    Route::post('/eventos/{evento}/inscribirse', [EventoController::class, 'inscribirse'])->name('eventos.inscribirse');
    Route::delete('/eventos/{evento}/desapuntarse', [EventoController::class, 'desapuntarse'])->name('eventos.desapuntarse');
});

// 🔹 Rutas protegidas para administradores
Route::middleware(['auth', 'verified', 'registro.completo', 'admin'])->prefix('admin')->group(function () {
    // Gestión de ponentes
    Route::get('/GestionPonentes', [PonenteController::class, 'adminIndex'])->name('admin.ponentes.index');
    Route::get('/ponentes/create', [PonenteController::class, 'create'])->name('admin.ponentes.create');
    Route::post('/ponentes', [PonenteController::class, 'store'])->name('admin.ponentes.store');
    Route::delete('/ponentes/{ponente}', [PonenteController::class, 'destroy'])->name('admin.ponentes.destroy');
    Route::get('/ponentes/{ponente}', [PonenteController::class, 'edit'])->name('admin.ponentes.edit');
    Route::put('/ponentes/{ponente}', [PonenteController::class, 'update'])->name('admin.ponentes.update');

    // Gestión de eventos
    Route::get('/GestionEventos', [EventoController::class, 'adminIndex'])->name('admin.eventos.index');
    Route::get('/eventos/create', [EventoController::class, 'create'])->name('admin.eventos.create');
    Route::get('/eventos/edit/{evento}', [EventoController::class, 'edit'])->name('admin.eventos.edit');
    Route::put('/eventos/{evento}', [EventoController::class, 'update'])->name('admin.eventos.update');
    Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('admin.eventos.destroy');

    // Gestión de asistencias
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('admin.asistencias.index');
    Route::get('/asistencias/destroy', [AsistenciaController::class, 'destroy'])->name('admin.asistencias.destroy');
});

