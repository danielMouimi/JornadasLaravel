<?php

use App\Http\Controllers\Home\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// forrequest para validaciones de la api
// controlar el borrado de eventos (usuario y ponente al la inscripcion) //hecho comprobar
// proteger peticiones a la api
// las fotos (material de belen)


use App\Http\Controllers\PonenteController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\DashboardController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PayPalController;



Route::middleware('auth:sanctum')->post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::middleware('auth:sanctum')->post('/paypal/capture/{orderID}', [PayPalController::class, 'captureOrder']);

Route::middleware('auth:sanctum')->post('/procesarPago', [PagoController::class, 'procesarPago']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::get('/ponentes', [PonenteController::class, 'index_api']);
Route::middleware('auth:sanctum')->get('/ponentes/{id}', [PonenteController::class, 'show_api']);
Route::middleware(['auth:sanctum','admin'])->post('/ponentes', [PonenteController::class, 'create_api']);
Route::middleware(['auth:sanctum','admin'])->put('/ponentes/{id}', [PonenteController::class, 'update_api']);
Route::middleware(['auth:sanctum','admin'])->delete('/ponentes/{id}', [PonenteController::class, 'destroy_api']);



// Rutas para asistencias
Route::get('/asistencias', [AsistenciaController::class, 'index_api']);
Route::get('/asistencias/{id}', [AsistenciaController::class, 'show_api']);

// Rutas para el dashboard del usuario
Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'index_api']);
Route::get('/home', [HomeController::class, 'index_api']);



//eventos
Route::get('/eventos', [EventoController::class, 'index_api']);
Route::middleware('auth:sanctum')->get('/eventos/{id}', [EventoController::class, 'show_api']);
Route::middleware(['auth:sanctum','admin'])->post('/eventos', [EventoController::class, 'store_api']);
Route::middleware(['auth:sanctum','admin'])->put('/eventos/{id}', [EventoController::class, 'update_api']);
Route::middleware(['auth:sanctum','admin'])->delete('/eventos/{id}', [EventoController::class, 'destroy_api']);



Route::middleware('auth:sanctum')->post('/eventos/{evento}/inscribirse', [EventoController::class, 'inscribirse_api']);

// Ruta para desapuntarse de un evento
Route::middleware('auth:sanctum')->delete('/eventos/{evento}/desapuntarse', [EventoController::class, 'desapuntarse_api']);

// Ruta para obtener las inscripciones del usuario
Route::middleware('auth:sanctum')->get('/user/inscripciones', [AuthController::class, 'inscripciones']);


    // 🔹 Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);
//});
