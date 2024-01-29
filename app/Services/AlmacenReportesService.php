<?php

namespace App\Services;

use App\Models\SucursalProducto;
use App\Models\SucursalProductoHistorial;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class AlmacenReportesService {
    public function stock(string $sucursal){
        $sqlWhere = [];
        if ($sucursal != ""){
            $sqlWhere["id_sucursal"] = $sucursal;
        }

        return SucursalProducto::where($sqlWhere)
                        ->join("productos as p",  function($join){
                            $join->on("p.id","=","sucursal_productos.id_producto");
                        })
                        ->join("categoria_productos as cp",  function($join){
                            $join->on("cp.id","=","p.id_categoria_producto");
                        })
                        ->join("tipo_categorias as tc",  function($join){
                            $join->on("tc.id","=","cp.id_tipo_categoria");
                        })
                        ->join("sucursals as suc",  function($join){
                            $join->on("suc.id","=","sucursal_productos.id_sucursal");
                        })
                        ->groupBy("id_producto", "p.nombre", "fecha_vencimiento", "lote", "cp.nombre", "tc.nombre" )
                        ->orderBy("p.nombre")
                        ->get([
                            "p.nombre as producto",
                            "fecha_vencimiento",
                            "sucursal_productos.lote as lote",
                            "cp.nombre as categoria",
                            "tc.nombre as tipo",
                            DB::raw("SUM(stock) as stock"),
                            DB::raw("AVG(sucursal_productos.precio_entrada) as precio_entrada_promedio")
                        ]);
    }

    public function kardex(string $sucursal, string $producto){
        $sqlWhere = [];
        if ($sucursal != ""){
            $sqlWhere["id_sucursal"] = $sucursal;
        }

        if ($producto != ""){
            $sqlWhere["id_producto"] = $producto;
        }

        return SucursalProductoHistorial::where($sqlWhere)
                        ->join("productos as p",  function($join){
                            $join->on("p.id","=","sucursal_producto_historials.id_producto");
                        })
                        ->leftJoin("sucursals as suc_destino",  function($join){
                            $join->on("suc_destino.id","=","sucursal_producto_historials.id_sucursal_destino");
                        })
                        ->leftJoin("sucursals as suc_origen",  function($join){
                            $join->on("suc_origen.id","=","sucursal_producto_historials.id_sucursal_origen");
                        })
                        ->orderBy("fecha_movimiento")
                        ->orderBy("sucursal_producto_historials.id")
                        ->get([
                            "sucursal_producto_historials.id",
                            "fecha_movimiento",
                            DB::raw("(CASE tipo_movimiento WHEN 'S' THEN suc_origen.nombre  ELSE suc_destino.nombre END) as sucursal"),
                            "p.nombre as producto",
                            "fecha_vencimiento",
                            "lote as lote",
                            DB::raw("COALESCE(precio_entrada, '-') as precio_entrada"),
                            DB::raw("COALESCE(precio_salida, '-') as precio_salida"),
                            DB::raw("IF(tipo_movimiento = 'E', cantidad, cantidad * -1) as cantidad"),
                            DB::raw("(CASE tipo_movimiento WHEN 'E' THEN 'INGRESO' ELSE 'SALIDA' END) as movimiento"),
                            DB::raw("(COALESCE(precio_entrada, precio_salida) * IF(tipo_movimiento = 'E', cantidad, cantidad * -1)) as totalizado"),
                        ]);

    }

}
