<?php

namespace App\Http\Controllers;

use App\Http\Requests\VentaRequest;
use App\Models\User;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{

    public function index(Request $request){
        $data = $request->validate([
            "id_sucursal"=>"required|integer",
            "fecha_inicio"=>"required|date",
            "fecha_fin"=>"required|date"
        ]);

        return Venta::with(["cliente"=>function($q){
                    $q->select("id", "numero_documento",DB::raw("CONCAT(nombres,' ',apellidos) as nombres_apellidos"));
                }])
                ->whereBetween('fecha_venta', [$data["fecha_inicio"], $data["fecha_fin"]])
                ->where(["id_sucursal"=>$data["id_sucursal"]])
                ->select(
                        "id",
                        "id_tipo_comprobante", "serie", "correlativo",
                        "id_cliente",
                        "monto_efectivo", "monto_credito", "monto_tarjeta", "monto_yape", "monto_plin", "monto_transferencia","monto_descuento",
                        "monto_total_venta", "fecha_venta", "hora_venta"
                        )
                ->orderBy("fecha_venta", "DESC")
                ->orderBy("hora_venta", "DESC")
                ->get();
    }

    public function store(VentaRequest $request){
        $data = $request->validated();
        $data["id_usuario_registro"] = $request->user->id;

        DB::beginTransaction();
        $venta = (new VentaService)->registrar($data);
        DB::commit();

        return $venta;
    }

    public function show(string $id){
        return Venta::with([
                    "cliente"=>function($q){
                        $q->select("id", "numero_documento",DB::raw("CONCAT(nombres,' ',apellidos) as nombres_apellidos"));
                    },
                    "detalle"=>function($q){
                        $q->select("id_venta", "item", "descripcion_producto as producto", "cantidad", "lote", "subtotal","precio_venta_unitario as precio_unitario");
                        $q->orderBy("item");
                    },
                    "comprobante"=>function($q){
                        $q->select("id", "id_atencion",);
                    }
                ])
                ->findOrFail($id,
                [
                    "id",
                    "id_tipo_comprobante", "serie", "correlativo",
                    "id_cliente",
                    "monto_efectivo", "monto_credito", "monto_tarjeta", "monto_yape", "monto_plin", "monto_transferencia","monto_descuento",
                    "monto_total_venta", "fecha_venta", "hora_venta",
                    DB::raw("(monto_total_venta - monto_descuento) as subtotal")
                ]
            );
    }

    public function destroy(string $id){
        $venta = Venta::findOrFail($id);
        DB::beginTransaction();
        $venta = (new VentaService)->anular($venta);
        DB::commit();
        return $venta;
    }

    public function obtenerVentaTicket(Request $request, string $id){
        $user = $request->user();
        $venta = Venta::with([
                    "cliente"=>function($q){
                        $q->select("id", "numero_documento",DB::raw("CONCAT(nombres,' ',apellidos) as nombres_completos"));
                    },
                    "detalle"=>function($q){
                        $q->select("id", "id_venta", "descripcion_producto as nombre_servicio", "cantidad", "precio_venta_unitario as precio_unitario");
                        $q->orderBy("item");
                    },
                ])
                ->findOrFail($id, [
                    "id",
                    "id_tipo_comprobante", "serie", "correlativo",
                    "id_cliente",
                    "observaciones",
                    "monto_efectivo", "monto_credito", "monto_tarjeta", "monto_yape", "monto_plin",
                    "monto_transferencia as monto_deposito","monto_descuento",
                    "monto_total_venta", "hora_venta","id_usuario_registro",
                    DB::raw("DATE_FORMAT(fecha_venta,'%d-%m-%Y') as fecha_venta")
                ]);

        $usuario_registro = User::find($venta->id_usuario_registro)->nombres_apellidos;
        $venta->usuario_impresion = $user->nombres_apellidos;
        $venta->usuario_registro = $usuario_registro;
        return $venta;
    }
}
