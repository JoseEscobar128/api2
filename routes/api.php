<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\EstadoPedidoController;
use App\Http\Controllers\Api\ModalidadPedidoController;
use App\Http\Controllers\Api\MesaController;
use App\Http\Controllers\Api\AsistenciaController;
use App\Http\Controllers\Api\DashboardController;

// Agrupamos todas las rutas protegidas para simplificar la configuración
Route::middleware(['auth:sanctum'])->prefix('v2')->group(function () {
    
    // CRUD de Productos
    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->middleware('permission:productos.view_all');
        Route::post('/', [ProductoController::class, 'store'])->middleware('permission:productos.create');
        Route::get('/{id}', [ProductoController::class, 'show'])->middleware('permission:productos.view');
        Route::put('/{id}', [ProductoController::class, 'update'])->middleware('permission:productos.update');
        Route::delete('/{id}', [ProductoController::class, 'destroy'])->middleware('permission:productos.delete');
    });

    // CRUD de Sucursales
    Route::prefix('sucursales')->group(function () {
        Route::get('/', [SucursalController::class, 'index'])->middleware('permission:sucursales.view_all');
        Route::post('/', [SucursalController::class, 'store'])->middleware('permission:sucursales.create');
        Route::get('/{id}', [SucursalController::class, 'show'])->middleware('permission:sucursales.view');
        Route::put('/{id}', [SucursalController::class, 'update'])->middleware('permission:sucursales.update');
        Route::delete('/{id}', [SucursalController::class, 'destroy'])->middleware('permission:sucursales.delete');
    });

    // CRUD de Categorías
    Route::prefix('categorias')->group(function () {
        Route::get('/', [CategoriaController::class, 'index'])->middleware('permission:categorias.view_all');
        Route::post('/', [CategoriaController::class, 'store'])->middleware('permission:categorias.create');
        Route::get('/{id}', [CategoriaController::class, 'show'])->middleware('permission:categorias.view');
        Route::put('/{id}', [CategoriaController::class, 'update'])->middleware('permission:categorias.update');
        Route::delete('/{id}', [CategoriaController::class, 'destroy'])->middleware('permission:categorias.delete');
    });

    
    Route::prefix('dashboard')->group(function () {
    Route::get('/summary', [DashboardController::class, 'summary'])->middleware('permission:dashboard.view');
    });

    // Rutas de Pedidos
    Route::prefix('pedidos')->group(function () {
        Route::get('/', [PedidoController::class, 'index'])->middleware('permission:pedidos.view_all');
        Route::post('/', [PedidoController::class, 'store'])->middleware('permission:pedidos.create');
        Route::get('/{id}', [PedidoController::class, 'show'])->middleware('permission:pedidos.view');
        Route::patch('/{id}/estado', [PedidoController::class, 'actualizarEstado'])->middleware('permission:pedidos.update_estado');
    });

    Route::post('/pedidoscliente', [PedidoController::class, 'storeCliente']);
    Route::get('/pedidoscliente/{id}', [PedidoController::class, 'showCliente']);

        // 💡 RUTAS FALTANTES: Listar estados y modalidades de pedido
    Route::prefix('estado_pedidos')->group(function () {
        Route::get('/', [EstadoPedidoController::class, 'index']);
    });
    
    Route::prefix('modalidades_pedido')->group(function () {
        Route::get('/', [ModalidadPedidoController::class, 'index']);
    });

    Route::prefix('mesas')->group(function () {
    Route::get('/', [MesaController::class, 'index']);           
    Route::get('/{id}', [MesaController::class, 'show']);     
    Route::put('/{id}', [MesaController::class, 'update']);  

    });

    Route::prefix('asistencia')->group(function () {
    // Listar asistencias con filtros y paginación
    Route::get('/', [AsistenciaController::class, 'index'])->middleware('permission:asistencias.view_all');

    // Sincronizar asistencias (desde la app de escritorio, por ejemplo)
    Route::post('/', [AsistenciaController::class, 'sincronizarRegistros'])->middleware('permission:asistencias.create');
    });
});
// Ruta de login (sin protección)
Route::prefix('v2')->group(function () {
    Route::any('/login', function () {
        return response()->json([
            'status' => 'error',
            'message' => 'Debes estar autenticado para usar esta ruta.'
        ], 401);
    })->name('login');
});
