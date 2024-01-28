<?php

namespace App\Http\Controllers;

use App\Models\DocumentoElectronico;
use App\Models\User;
use App\Services\DocumentoElectronicoService;
use App\Services\DocumentoElectronicoXMLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentoElectronicoController extends Controller
{
    public function obtenerComprobanteTicket(Request $request, string $id){
        $user = $request->user();
        $doc = DocumentoElectronico::with([
                    "detalle"=>function($q){
                        $q->select("id", "id_documento_electronico", "descripcion_item", "cantidad_item", "precio_venta_unitario");
                        $q->orderBy("item");
                    },
                ])
                ->findOrFail($id, [
                    "id",
                    "id_tipo_comprobante", "serie", "correlativo",
                    "id_tipo_documento_cliente", "numero_documento_cliente", "descripcion_cliente", "direccion_cliente",
                    "hora_emision",
                    "id_tipo_moneda",
                    "total_gravadas", "total_inafectas", "total_exoneradas",
                    "importe_credito", "descuento_global", "importe_total",
                    "total_igv",
                    "serie_comprobante_modifica","correlativo_comprobante_modifica","descripcion_motivo_nota",
                    "condicion_pago", "observaciones", "es_delivery",
                    "total_letras",
                    "valor_firma", "valor_resumen",
                    "id_usuario_registro",
                    "fecha_emision as fecha_emision_raw",
                    DB::raw("DATE_FORMAT(fecha_emision,'%d-%m-%Y') as fecha_emision"),
                ]);

        $usuario_registro = User::find($doc->id_usuario_registro)->nombres_apellidos;
        $doc->usuario_impresion = $user->nombres_apellidos;
        $doc->usuario_registro = $usuario_registro;
        return $doc;
    }

    public function obtenerComprobantesParaGeneracion(Request $request){
        $data = $request->validate([
            "fecha_inicio"=>"required|date",
            "fecha_fin"=>"required|date",
            "estado"=>"required|string|size:1",
            "todas_fechas"=>"required|integer"
        ]);

        $docs = DocumentoElectronico::select(
                    "id",
                    "id_tipo_comprobante",
                    "numero_documento_cliente", "descripcion_cliente",
                    "id_tipo_moneda",
                    "total_gravadas", "descuento_global", "total_igv", "importe_total", "xml_filename",
                    "fue_generado","fue_firmado", "cdr_estado",
                    "enviar_a_sunat",
                    DB::raw("CONCAT(serie,'-',LPAD(correlativo, 6, '0')) as comprobante"),
                    DB::raw("DATE_FORMAT(fecha_emision,'%d-%m-%Y') as fecha_emision")
                );

        if ($data["todas_fechas"] == 0){
            $docs = $docs->whereBetween("fecha_emision", [$data["fecha_inicio"], $data["fecha_fin"]]);
        }

        switch($data["estado"]){
            case "P":
                $docs = $docs->where(["enviar_a_sunat"=>"0"]);
                break;
            case "F":
                $docs = $docs->where(["enviar_a_sunat"=>"0", "fue_firmado"=>"1"]);
                break;
            case "A":
                $docs = $docs->where(["enviar_a_sunat"=>"1", "cdr_estado"=>"0"]);
                break;
            case "R":
                $docs = $docs->where(["enviar_a_sunat"=>"1"])
                                ->where("cdr_estado", "!=", "0")
                                ->whereNotNull("cdr_estado");
                break;
            case "T":
                break;
        }

        $docs = $docs->get();
        return $docs;
    }

    public function generarComprobanteXML(string $id){
        return (new DocumentoElectronicoXMLService)->generarComprobanteXML($id);
    }

    public function firmarComprobanteXML(string $id){
        return (new DocumentoElectronicoXMLService)->firmarComprobanteXML($id);
    }

    public function generarYFirmarComprobanteXML(string $id){
        $documentoElectronicoService = new DocumentoElectronicoXMLService;

        DB::beginTransaction();
        $generado = $documentoElectronicoService->generarComprobanteXML($id);
        $firmado = $documentoElectronicoService->firmarComprobanteXML($id);
        DB::commit();

        return [
            "generado"=>$generado,
            "firmado"=>$firmado
        ];
    }
}
