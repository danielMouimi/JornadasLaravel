<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PonenteController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\AsistenciaController;
use App\Mail\EnviarTicket;

// 🔹 Ruta principal (Landing Page)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// 🔹 Rutas de autenticación (Generadas por Laravel Breeze)
require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pago', [PagoController::class, 'index'])->name('pago.index');
    Route::post('/pago/procesar', [PagoController::class, 'procesarPago'])->name('pago.procesar');
    Route::middleware('auth:sanctum')->get('/pagado', [EnviarTicket::class, 'build'])->name('pagado');
});




// 🔹 Rutas protegidas para usuarios autenticados


    // Dashboard del usuario
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ponentes (Accesibles para cualquier usuario autenticado)
    Route::get('/ponentes', [PonenteController::class, 'index'])->name('ponentes.index');
    Route::get('/ponentes/{ponente}', [PonenteController::class, 'edit'])->name('admin.ponentes.edit');
    Route::put('/ponentes/{ponente}', [PonenteController::class, 'update'])->name('admin.ponentes.update');


    // Eventos (Accesibles para cualquier usuario autenticado)
    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
    Route::post('/eventos/{evento}/inscribirse', [EventoController::class, 'inscribirse'])->name('eventos.inscribirse');
    Route::delete('/eventos/{evento}/desapuntarse', [EventoController::class, 'desapuntarse'])->name('eventos.desapuntarse');

    // Pagos (Solo para usuarios autenticados)
//    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
//    Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
//    Route::post('/pagos/procesar', [PagoController::class, 'store'])->name('pagos.store');



// 🔹 Rutas protegidas para administradores
// ['auth', 'admin'] deveria funcionar pero no esta encontrando admin asique protegeremos admin en las vistas
Route::middleware(['auth'])->prefix('admin')->group(function () {
        Route::get('/GestionPonentes', [PonenteController::class, 'adminIndex'])->name('admin.ponentes.index');
    // Gestión de ponentes
    Route::get('/ponentes/create', [PonenteController::class, 'create'])->name('admin.ponentes.create');
    Route::post('/ponentes', [PonenteController::class, 'store'])->name('admin.ponentes.store');

    Route::delete('/ponentes/{ponente}', [PonenteController::class, 'destroy'])->name('admin.ponentes.destroy');

    // Gestión de eventos
    Route::get('/GestionEventos', [EventoController::class, 'adminIndex'])->name('admin.eventos.index');
    Route::get('/eventos/create', [EventoController::class, 'create'])->name('admin.eventos.create');
    Route::get('/eventos/edit/{evento}', [EventoController::class, 'edit'])->name('admin.eventos.edit');
    Route::put('/eventos/{evento}', [EventoController::class, 'update'])->name('admin.eventos.update');
    Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('admin.eventos.destroy');

    // Gestión de pagos
//    Route::get('/pagos', [PagoController::class, 'adminIndex'])->name('admin.pagos.index');
//    Route::get('/pagos/{pago}/edit', [PagoController::class, 'edit'])->name('admin.pagos.edit');
//    Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('admin.pagos.update');
//    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('admin.pagos.destroy');

    Route::get('/admin/asistencias', [AsistenciaController::class, 'index'])->name('admin.asistencias.index');
    Route::get('/admin/asistencias/destroy', [AsistenciaController::class, 'destroy'])->name('admin.asistencias.destroy');

});
