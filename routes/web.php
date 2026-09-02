<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MovimientoInventarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---- Empresas (solo Super Admin gestiona globalmente) ----
    Route::resource('empresas', EmpresaController::class);

    // ---- Sucursales ----
    Route::resource('sucursales', SucursalController::class);

    // ---- Áreas ----
    Route::resource('areas', AreaController::class);

    // ---- Categorías ----
    Route::resource('categorias', CategoriaController::class);

    // ---- Unidades de medida ----
    Route::resource('unidades-medida', UnidadMedidaController::class);

    // ---- Proveedores ----
    Route::resource('proveedores', ProveedorController::class);

    // ---- Ítems ----
    Route::resource('items', ItemController::class);

    // ---- Movimientos ----
    Route::get('/movimientos', [MovimientoInventarioController::class, 'index'])->name('movimientos.index');
    Route::get('/movimientos/entrada', [MovimientoInventarioController::class, 'indexEntrada'])->name('movimientos.entrada');
    Route::get('/movimientos/salida', [MovimientoInventarioController::class, 'indexSalida'])->name('movimientos.salida');
    Route::get('/movimientos/traslado', [MovimientoInventarioController::class, 'indexTraslado'])->name('movimientos.traslado');
    Route::get('/movimientos/ajuste', [MovimientoInventarioController::class, 'indexAjuste'])->name('movimientos.ajuste');
    Route::post('/movimientos/entrada', [MovimientoInventarioController::class, 'entrada'])->name('movimientos.entrada.store');
    Route::post('/movimientos/salida', [MovimientoInventarioController::class, 'salida'])->name('movimientos.salida.store');
    Route::post('/movimientos/traslado', [MovimientoInventarioController::class, 'traslado'])->name('movimientos.traslado.store');
    Route::post('/movimientos/ajuste', [MovimientoInventarioController::class, 'ajuste'])->name('movimientos.ajuste.store');
    Route::get('/movimientos/stock-disponible', [MovimientoInventarioController::class, 'stockDisponible'])
        ->name('movimientos.stock');

    // ---- Reportes ----
    Route::get('/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::get('/reportes/movimientos', [ReporteController::class, 'movimientos'])->name('reportes.movimientos');
    Route::get('/reportes/inventario/exportar-excel', [ReporteController::class, 'exportarInventarioExcel'])->name('reportes.inventario.excel');
    Route::get('/reportes/inventario/exportar-pdf', [ReporteController::class, 'exportarInventarioPdf'])->name('reportes.inventario.pdf');
    Route::get('/reportes/movimientos/exportar-excel', [ReporteController::class, 'exportarMovimientosExcel'])->name('reportes.movimientos.excel');
    Route::get('/reportes/movimientos/exportar-pdf', [ReporteController::class, 'exportarMovimientosPdf'])->name('reportes.movimientos.pdf');

    // ---- Usuarios ----
    Route::resource('usuarios', UserController::class);
});

require __DIR__ . '/auth.php';