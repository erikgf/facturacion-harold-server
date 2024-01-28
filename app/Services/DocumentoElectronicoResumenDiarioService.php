<?php

namespace App\Services;

use App\Models\DocumentoElectronico;
use App\Models\DocumentoElectronicoResumenDiario;
use App\Models\DocumentoElectronicoResumenDiarioDetalle;
use App\Models\EmpresaFacturacion;
use Illuminate\Support\Str;

class DocumentoElectronicoResumenDiarioService {

    private $CODIGO_COMPROBANTE = "RC";

    public function listar(string $fechaInicio, string $fechaFin){
        return DocumentoElectronicoResumenDiario::whereBetween("fecha_emision", [$fechaInicio, $fechaFin])
                ->select("id",
                        "nombre_resumen",
                        "numero_envios", "ticket", "cdr_estado", "cdr_descripcion",
                        "fecha_emision","fecha_generacion",
                        "enviar_a_sunat")
                ->get();
    }

    public function leer(string $id){
        return DocumentoElectronicoResumenDiario::with([
            "detalle"=>function($q){
                $q->select("id","id_documento_electronico_resumen_diario",
                            "item", "id_tipo_comprobante", "serie_comprobante", "correlativo_comprobante",
                            "status", "id_tipo_moneda", "importe_gravadas", "importe_igv", "importe_total");
                $q->orderBy("item");
            }
        ])->findOrFail($id, [
            "id",
            "nombre_resumen",
            "numero_envios",
            "ticket",
            "cdr_estado",
            "cdr_descripcion",
            "fecha_emision",
            "fecha_generacion",
            "enviar_a_sunat"
        ]);
    }

    public function anular(string $id){
        $doc = DocumentoElectronicoResumenDiario::findOrFail($id, ["id", "nombre_resumen", "ticket", "cdr_estado"]);

        if ($doc->ticket){
            throw new \Exception("El Resumen Diario $doc->nombre_resumen anulado ya tiene un TICKET en espera. No se puede eliminar.", 1);
        }

        if ($doc->cdr_estado === "0"){
            throw new \Exception("El Resumen Diario $doc->nombre_resumen anulado ya está informado a SUNAT. No se puede eliminar.", 1);
        }

        $doc->delete();
        return $doc;
    }

    public function obtenerComprobantesPorFecha(string $fecha){
        //Obtener comprobantes 03 y 07 afectando a 03, con fecha indicada Y sin CDR.
        return DocumentoElectronico::where([
                    "fecha_emision"=>$fecha,
                ])
                ->whereNull('cdr_estado')
                ->whereIn("id_tipo_comprobante", ["03","07","08"])
                ->whereRaw("LEFT(serie, 1) = 'B'")
                ->select("id",
                            "id_tipo_comprobante", "serie", "correlativo",
                            "id_tipo_documento_cliente","numero_documento_cliente", "id_tipo_moneda",
                            "total_gravadas", "total_inafectas", "total_exoneradas", "total_isc",
                            "total_igv", "importe_total",
                            "id_tipo_comprobante_modifica", "serie_comprobante_modifica", "correlativo_comprobante_modifica")
                ->get();
    }

    public function registrar(array $data, $deboGenerar = true, $deboFirmar = true){
        $rd = new DocumentoElectronicoResumenDiario();

        $rd->codigo = $this->CODIGO_COMPROBANTE;
        $rd->fecha_generacion = date('Y-m-d');
        $rd->serie = Str::replace('-', '', $rd->fecha_generacion);

        $ruc_empresa = EmpresaFacturacion::findOrFail(1, ["nro_documento_empresa"])->nro_documento_empresa;
        $rd->secuencia = DocumentoElectronicoResumenDiario::where(["serie"=>$rd->serie])->max("secuencia") + 1;

        $rd->nombre_resumen =  implode("-", [
            $ruc_empresa,
            $rd->codigo,
            $rd->serie,
            $rd->secuencia
        ]);

        $rd->fecha_emision = $data["fecha_emision"];
        $status = $data["status"];

        $rd->save();

        $comprobantes = collect($data["comprobantes"]);
        $comprobantes->transform(function ($item, int $key) {
            return $item["id"];
        });

        $comprobantesRegistrar = DocumentoElectronico::whereIn("id", $comprobantes->all())
                                        ->select("id",
                                            "id_tipo_comprobante", "serie", "correlativo",
                                            "id_tipo_documento_cliente",
                                            "numero_documento_cliente", "id_tipo_moneda",
                                            "total_gravadas", "total_inafectas",
                                            "total_exoneradas",
                                            "total_otro_imp",
                                            "total_isc","total_igv", "importe_total",
                                            "id_tipo_comprobante_modifica", "serie_comprobante_modifica", "correlativo_comprobante_modifica")
                                        ->get();

        foreach($comprobantesRegistrar as $i => $comprobante){
            $item = $i + 1;
            $rdDetalle = new DocumentoElectronicoResumenDiarioDetalle;
            $rdDetalle->id_documento_electronico_resumen_diario = $rd->id;
            $rdDetalle->id_documento_electronico = $comprobante->id;
            $rdDetalle->item = $item;
            $rdDetalle->id_tipo_comprobante = $comprobante->id_tipo_comprobante;
            $rdDetalle->serie_comprobante = $comprobante->serie;
            $rdDetalle->correlativo_comprobante = $comprobante->correlativo;
            $rdDetalle->id_tipo_documento_cliente = $comprobante->id_tipo_documento_cliente;
            $rdDetalle->numero_documento_cliente = $comprobante->numero_documento_cliente;
            $rdDetalle->id_tipo_comprobante_modificado = $comprobante->id_tipo_comprobante_modifica;
            $rdDetalle->serie_comprobante_modificado  = $comprobante->serie_comprobante_modifica;
            $rdDetalle->correlativo_comprobante_modificado  = $comprobante->correlativo_comprobante_modifica;
            $rdDetalle->status = $status;
            $rdDetalle->id_tipo_moneda = $comprobante->id_tipo_moneda;
            $rdDetalle->importe_gravadas = $comprobante->total_gravadas;
            $rdDetalle->importe_exoneradas = $comprobante->total_exoneradas;
            $rdDetalle->importe_inafectas = $comprobante->total_inafectas;
            $rdDetalle->importe_otros = $comprobante->total_otro_imp;
            $rdDetalle->importe_igv = $comprobante->total_igv;
            $rdDetalle->importe_isc = $comprobante->total_isc;
            $rdDetalle->importe_total = $comprobante->importe_total;
            $rdDetalle->save();
        }

        if ($deboGenerar){
            $rd->generado = (new DocumentoElectronicoResumenDiarioXMLService)->generarComprobanteXML($rd->id);
            if ($rd->generado["fue_generado"] == 0){
                throw new \Exception("Ha ocurrido un problema al generar el Resumen Diario.", 1);
            }
        }

        if ($deboFirmar){
            $rd->firmado = (new DocumentoElectronicoResumenDiarioXMLService)->firmarComprobanteXML($rd->id);
            if ($rd->firmado["valor_firma"] == ""){
                throw new \Exception("Ha ocurrido un problema al generar el Resumen Diario.", 1);
            }
        }

        return $rd;
    }

}
