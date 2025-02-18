<?php

use App\Http\Controllers\Home\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



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

// 🔹 Autenticación
Route::get('/user', function () {
    return response()->json(Auth::user());
});


Route::middleware('auth:sanctum')->post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::middleware('auth:sanctum')->post('/paypal/capture/{orderID}', [PayPalController::class, 'captureOrder']);



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/ponentes', [PonenteController::class, 'index_api']);
Route::get('/ponentes/{id}', [PonenteController::class, 'show_api']);
Route::post('/ponentes', [PonenteController::class, 'create_api']);
Route::put('/ponentes/{id}', [PonenteController::class, 'update_api']);
Route::delete('/ponentes/{id}', [PonenteController::class, 'destroy_api']);



// Rutas para asistencias
Route::get('/asistencias', [AsistenciaController::class, 'index_api']);
Route::get('/asistencias/{id}', [AsistenciaController::class, 'show_api']);

// Rutas para el dashboard del usuario
Route::get('/dashboard', [DashboardController::class, 'index_api']);
Route::get('/home', [HomeController::class, 'index_api']);



//eventos
Route::get('/eventos', [EventoController::class, 'index_api']);
Route::get('/eventos/{id}', [EventoController::class, 'show_api']);
Route::post('/eventos', [EventoController::class, 'store_api']);
Route::put('/eventos/{id}', [EventoController::class, 'update_api']);
Route::delete('/eventos/{id}', [EventoController::class, 'destroy_api']);



Route::middleware('auth:sanctum')->post('/eventos/{evento}/inscribirse', [EventoController::class, 'inscribirse_api']);

// Ruta para desapuntarse de un evento
Route::middleware('auth:sanctum')->delete('/eventos/{evento}/desapuntarse', [EventoController::class, 'desapuntarse_api']);

// Ruta para obtener las inscripciones del usuario
Route::middleware('auth:sanctum')->get('/user/inscripciones', [AuthController::class, 'inscripciones']);


// 🔹 Rutas protegidas con autenticación
//Route::middleware(['auth:sanctum'])->group(function () {
//
//    // Usuario
//    Route::get('/user', function (Request $request) {
//        return $request->user();
//    });
//
//    // 🔹 Ponentes
////    Route::apiResource('ponentes', PonenteController::class);
//
//
//    // 🔹 Eventos
//    Route::apiResource('eventos', EventoController::class);
//    Route::post('/eventos/{evento}/inscribirse', [EventoController::class, 'inscribirse']);
//    Route::delete('/eventos/{evento}/desapuntarse', [EventoController::class, 'desapuntarse']);
//
//
//    // 🔹 Rutas para administradores (middleware adicional si es necesario)
//    Route::middleware(['admin'])->prefix('admin')->group(function () {
////        Route::apiResource('ponentes', PonenteController::class)->except(['index', 'show']);
//        Route::apiResource('eventos', EventoController::class)->except(['index', 'show']);
//        Route::apiResource('pagos', PagoController::class)->except(['store']);
//        Route::get('/asistencias', [AsistenciaController::class, 'index']);
//        Route::delete('/asistencias/{asistencia}', [AsistenciaController::class, 'destroy']);
//    });

    // 🔹 Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);
//});
