<?php

use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DocumentoElectronicoController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PermisoRolController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\SucursalProductoController;
use App\Http\Controllers\TipoCategoriaController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
*/

Route::post("sesion/iniciar", [SesionController::class, "login"]);


Route::group(["middleware"=>['auth:sanctum']], function(){
    Route::apiResource("unidad-medidas", UnidadMedidaController::class);
    Route::apiResource("categorias", CategoriaProductoController::class);
    Route::apiResource("tipo-categorias", TipoCategoriaController::class);
    Route::apiResource("marcas", MarcaController::class);
    Route::apiResource("permisos", PermisoController::class);
    Route::apiResource("roles", RolController::class);
    Route::apiResource("proveedores", ProveedorController::class);
    Route::apiResource("clientes", ClienteController::class);
    Route::apiResource("sucursales", SucursalController::class);
    Route::apiResource("usuarios", UsuarioController::class);
    Route::apiResource("productos", ProductoController::class);

    Route::get("categorias/tipo/{tipo}", [CategoriaProductoController::class, "obtenerPorTipo"]);
    Route::get("permisos-rol/{idRol}", [PermisoRolController::class, "index"]);
    Route::post("permisos-rol/agregar", [PermisoRolController::class, "agregarPermiso"]);
    Route::post("permisos-rol/quitar", [PermisoRolController::class, "quitarPermiso"]);
    Route::post("usuarios/cambiar-clave", [UsuarioController::class, "cambiarClave"]);
    Route::get("compras-productos/{idSucursal}", [SucursalProductoController::class, "obtenerPorSucursalParaCompras"]);
    Route::get("ventas-productos/{idSucursal}", [SucursalProductoController::class, "obtenerPorSucursalParaVentas"]);
    Route::get("ventas-ticket/{id}", [VentaController::class, "obtenerVentaTicket"]);
    Route::get("comprobantes-ticket/{id}", [DocumentoElectronicoController::class, "obtenerComprobanteTicket"]);

    Route::prefix("almacen/")->group(function(){
        Route::get("productos-stock/{idSucursal}", [AlmacenController::class, "getProductosStockPorSucursal"]);
        Route::get("historial-productos/{idSucursal}", [AlmacenController::class, "getHistorialProductosPorSucursal"]);
    });

    Route::prefix("comprobantes/")->group(function(){
        Route::get("generacion", [DocumentoElectronicoController::class, "obtenerComprobantesParaGeneracion"]);
        Route::get("generar-xml/{id}", [DocumentoElectronicoController::class, "generarComprobanteXML"]);
        Route::get("firmar-xml/{id}", [DocumentoElectronicoController::class, "firmarComprobanteXML"]);
        Route::get("generar-firmar-xml/{id}", [DocumentoElectronicoController::class, "generarYFirmarComprobanteXML"]);
    });

    Route::apiResource("compras", CompraController::class);
    Route::apiResource("ventas", VentaController::class);

    Route::post("sesion/cerrar", [SesionController::class, "logout"]);
    Route::get("permisos-usuario", [PermisoRolController::class, "obtenerPermisos"]);
});
