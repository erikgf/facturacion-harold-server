<?php

namespace App\Http\Controllers;

use App\Models\SucursalProducto;
use App\Models\SucursalProductoHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function getProductosStockPorSucursal(string $idSucursal)
    {
        return SucursalProducto::with(["producto" => function($query){
                $query->select("id", "nombre", "id_categoria_producto", "id_marca");
                $query->with([
                    "marca"=>fn($q) => $q->select("id", "nombre"),
                    "categoria"=> fn($q) => $q->select("id", "nombre","id_tipo_categoria")
                ]);
                $query->orderBy("nombre");
            }])
            ->where(["id_sucursal"=>$idSucursal])
            ->orderBy("id_producto")
            ->get(["id", "id_producto", "stock", "fecha_vencimiento", "lote", DB::raw("CAST(precio_entrada as DECIMAL(10,2)) as precio")]);
    }

    public function getHistorialProductosPorSucursal(Request $request, string $idSucursal){
        $data = $request->validate([
            "fecha_inicio"=>"required|date",
            "fecha_fin"=>"required|date"
        ]);

        $fechaInicio = $data["fecha_inicio"];
        $fechaFin = $data["fecha_fin"];

        return SucursalProductoHistorial::with(["producto" => function($query){
                $query->select("id", "nombre", "id_categoria_producto");
                $query->with([
                    "categoria"=> function($query){
                        $query->select("id", "nombre","id_tipo_categoria");
                    }
                ]);
                $query->orderBy("nombre");
            }])
            ->whereRaw('COALESCE(id_sucursal_origen, id_sucursal_destino) = '.$idSucursal)
            ->whereBetween("fecha_movimiento", [$fechaInicio, $fechaFin])
            ->orderBy("id_producto")
            ->get(["id", "id_producto", "fecha_vencimiento", "lote", "cantidad", "fecha_movimiento",
                    DB::raw("CAST(precio_entrada as DECIMAL(10,2)) as precio_entrada"),
                    DB::raw("CAST(precio_salida as DECIMAL(10,2)) as precio_salida"),
                    DB::raw("IF(tipo_movimiento = 'E', 'ENTRADA', 'SALIDA') as movimiento"),
                    DB::raw("IF(id_venta IS NULL,'VENTA','COMPRA') as nota")
                    ]);
    }
}
