<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SucursalProductoController extends Controller
{
    public function obtenerPorSucursalParaCompras(string $idSucursal){
        Sucursal::findOrFail($idSucursal);

        $productos = DB::table("productos as p")
                ->leftJoin("sucursal_productos as sp", function($q) use($idSucursal){
                    $q->on("sp.id_producto","=","p.id");
                    $q->whereIn("sp.id_sucursal", [$idSucursal]);
                })
                ->join("categoria_productos as cp", function($q) {
                    $q->on("cp.id","=","p.id_categoria_producto");
                })
                ->join("marcas as m", function($q){
                    $q->on("m.id","=","p.id_marca");
                })
                ->groupBy("p.id", "p.codigo_generado", "p.precio_unitario", "p.nombre", "cp.id_tipo_categoria", "m.nombre","p.id_categoria_producto")
                ->select('p.id', "p.codigo_generado", 'p.precio_unitario', DB::raw('COALESCE(SUM(sp.stock),0) AS stock'), 'p.nombre as nombre_producto', 'm.nombre as marca', 'cp.id_tipo_categoria', 'p.id_categoria_producto as id_categoria')
                ->get();

        return $productos;
    }

    public function obtenerPorSucursalParaVentas(string $idSucursal){
        Sucursal::findOrFail($idSucursal);

        $productos = DB::table("productos as p")
                ->leftJoin("sucursal_productos as sp", function($q) use($idSucursal){
                    $q->on("sp.id_producto","=","p.id");
                    $q->whereIn("sp.id_sucursal", [$idSucursal]);
                })
                ->join("categoria_productos as cp", function($q) {
                    $q->on("cp.id","=","p.id_categoria_producto");
                })
                ->join("marcas as m", function($q){
                    $q->on("m.id","=","p.id_marca");
                })
                ->groupBy("p.id","p.codigo_generado",  "p.precio_unitario", "p.nombre", "cp.id_tipo_categoria", "m.nombre","p.id_categoria_producto","sp.fecha_vencimiento", "sp.lote")
                ->select('p.id', "p.codigo_generado", 'p.precio_unitario', DB::raw('COALESCE(SUM(sp.stock),0) AS stock'), 'p.nombre as nombre_producto', 'm.nombre as marca', 'cp.id_tipo_categoria', 'p.id_categoria_producto as id_categoria',
                            DB::raw("COALESCE(sp.fecha_vencimiento,'0000-00-00') AS fecha_vencimiento"),
                            DB::raw("COALESCE(sp.lote,'') AS lote"),
                            DB::raw("CONCAT(p.id, COALESCE(fecha_vencimiento,'0000-00-00'), COALESCE(lote,'')) as codigo_unico_producto"),
                        )
                ->get();

        return $productos;
    }
}
