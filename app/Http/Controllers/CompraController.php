<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompraRequest;
use App\Models\Compra;
use App\Services\CompraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{

    public function index(Request $request)
    {
        $data = $request->validate([
            "id_sucursal"=>"required|integer",
            "fecha_inicio"=>"required|date",
            "fecha_fin"=>"required|date"
        ]);

        return Compra::with(["proveedor"=>function($q){
                    $q->select("id", "numero_documento","razon_social");
                }])
                ->whereBetween('fecha_compra', [$data["fecha_inicio"], $data["fecha_fin"]])
                ->where(["id_sucursal"=>$data["id_sucursal"]])
                ->select(
                        "id",
                        "id_tipo_comprobante", "numero_comprobante",
                        "id_proveedor", "tipo_pago",
                        "importe_total", "fecha_compra", "hora_compra"
                        )
                ->orderBy("fecha_compra", "DESC")
                ->orderBy("hora_compra", "DESC")
                ->get();
    }

    public function store(CompraRequest $request){
        $data = $request->validated();
        $data["id_usuario_registro"] = $request->user->id;

        DB::beginTransaction();
        $compra = (new CompraService)->registrar($data);
        DB::commit();

        return $compra;
    }

    public function show(string $id)
    {
         $compra = Compra::with(["proveedor"=>function($q){
                        $q->select("id", "numero_documento","razon_social");
                    }, "detalle"=>function($q){
                        $q->select("id", "id_compra", "id_producto","item", "cantidad", "precio_unitario", DB::raw("(cantidad * precio_unitario) as subtotal"));
                        $q->with(["producto"=>function($q){
                            $q->select("id", "nombre");
                        }]);
                    }])
                ->select(
                        "id",
                        "id_tipo_comprobante", "numero_comprobante",
                        "id_proveedor", "tipo_pago", "tipo_tarjeta",
                        "importe_total", "fecha_compra", "hora_compra"
                        )
                ->findOrFail($id);

        return $compra;
    }

    public function destroy(string $id){
        $compra = Compra::findOrFail($id);
        DB::beginTransaction();
        $compra = (new CompraService)->anular($compra);
        DB::commit();
        return $compra;
    }
}
