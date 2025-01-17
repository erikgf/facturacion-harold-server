<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\DB;

class VentaReportesService {
    public function general(string $fechaDesde, string $fechaHasta, bool $todos, string $sucursal, string $cliente){
        $sqlWhere = [];
        if ($sucursal != ""){
            $sqlWhere["id_sucursal"] = $sucursal;
        }
        if ($cliente != ""){
            $sqlWhere["id_cliente"] = $cliente;
        }

        $queryCabecera = Venta::where($sqlWhere);

        if (!$todos){
            $queryCabecera->whereBetween("fecha_venta", [$fechaDesde, $fechaHasta]);
        }

        $cabecera = $queryCabecera->first([
                        DB::raw("COALESCE(SUM(monto_efectivo),0.00) as monto_efectivo"),
                        DB::raw("COALESCE(SUM(monto_credito),0.00) as monto_credito"),
                        DB::raw("COALESCE(SUM(monto_tarjeta),0.00) as monto_tarjeta"),
                        DB::raw("COALESCE(SUM(monto_plin),0.00) as monto_plin"),
                        DB::raw("COALESCE(SUM(monto_yape),0.00) as monto_yape"),
                        DB::raw("COALESCE(SUM(monto_transferencia),0.00) as monto_transferencia"),
                        DB::raw("COALESCE(SUM(sub_total),0.00) as sub_total"),
                        DB::raw("COALESCE(SUM(monto_descuento),0.00) as monto_descuento"),
                        DB::raw("COALESCE(SUM(monto_total_venta), 0.00) as total"),
                    ]);

        $queryDetalle = Venta::with([
            "cliente"=>function($q){
                $q->select("id", "nombres", "apellidos", "numero_documento");
            },
            "sucursal"=>function($q){
                $q->select("id", "nombre");
            }
        ]);

        $queryDetalle->where($sqlWhere)
                        ->leftJoin("documento_electronicos as de", function($join){
                            $join->on("de.id_atencion","=","ventas.id");
                        });

        if (!$todos){
            $queryDetalle->whereBetween("fecha_venta", [$fechaDesde, $fechaHasta]);
        }

        $detalle = $queryDetalle->orderBy("fecha_venta")
                            ->orderBy("hora_venta")
                            ->select(
                                "ventas.id",
                                "ventas.id_cliente", "ventas.id_sucursal",
                                "fecha_venta as fecha_venta_raw",
                                DB::raw("DATE_FORMAT(ventas.fecha_venta, '%d/%m/%Y') as fecha_venta"),
                                "ventas.monto_efectivo", "ventas.monto_credito", "ventas.monto_tarjeta", "ventas.monto_yape", "ventas.monto_plin", "ventas.monto_transferencia","ventas.monto_descuento",
                                "ventas.sub_total","ventas.monto_total_venta", "ventas.fecha_venta", "ventas.hora_venta",
                                DB::raw("COALESCE(de.id_tipo_comprobante, ventas.id_tipo_comprobante) as id_tipo_comprobante"),
                                DB::raw("CONCAT(COALESCE(de.serie,ventas.serie),'-', LPAD(COALESCE(de.correlativo, ventas.correlativo),6,'0')) as comprobante"),
                            )
                            ->get();

        return [
            "cabecera"=>$cabecera,
            "detalle"=>$detalle
        ];
    }

    public function masVendido(string $fechaDesde, string $fechaHasta, bool $todos, string $sucursal){
        $sqlWhere = [];
        if ($sucursal != ""){
            $sqlWhere["v.id_sucursal"] = $sucursal;
        }

        $query = VentaDetalle::join("ventas as v", (function($join){
                                    $join->on("v.id","=","venta_detalles.id");
                                }))
                                ->join("productos as p", function($join){
                                    $join->on("p.id","=","venta_detalles.id_producto");
                                })
                                ->join("sucursals as suc", function($join){
                                    $join->on("suc.id","=","v.id_sucursal");
                                })
                                ->where($sqlWhere)
                                ->whereNull("v.deleted_at");

        if (!$todos){
            $query->whereBetween("fecha_venta", [$fechaDesde, $fechaHasta]);
        }

        return $query->groupBy("p.id", "p.nombre", "p.codigo_generado", "suc.nombre")
                    ->orderBy("p.id")
                    ->get([
                        "p.id as id_producto",
                        "p.codigo_generado",
                        "p.nombre as producto",
                        "suc.nombre as sucursal",
                        DB::raw("COALESCE(SUM(precio_venta_unitario * cantidad),0.00) as monto_vendido"),
                        DB::raw("COALESCE(SUM(cantidad),0.00) as unidades_vendidas"),
                        DB::raw("COALESCE(SUM(costo_producto),0.00) as monto_gastado"),
                        DB::raw("COALESCE(SUM(precio_venta_unitario * cantidad - costo_producto),0.00) as utilidad"),
                    ]);
    }
}
