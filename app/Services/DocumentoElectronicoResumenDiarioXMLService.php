<?php

namespace App\Services;

use App\Models\DocumentoElectronicoResumenDiario;
use App\Models\EmpresaFacturacion;
use Illuminate\Support\Facades\DB;

class DocumentoElectronicoResumenDiarioXMLService {
    private $CODIGO_COMPROBANTE = "RC";
    private $RUTA_SISTEMA_FACTURACION = "http://localhost/andreitababy-facturacion/api/xml.generar.comprobante.php";
    private $RUTA_SISTEMA_FACTURACION_FIRMADO = "http://localhost/andreitababy-facturacion/api/xml.firmar.comprobante.php";
    //private $RUTA_SISTEMA_FACTURACION_ENVIAR = "http://localhost/andreitababy-facturacion/api/xml.enviar.comprobante.facturas.php";

    private function obtenerDatosParaCreacionXML(String $id){
        $rd = DocumentoElectronicoResumenDiario::with([
                    "detalle"=>function($q){
                        $q->select("id", "id_documento_electronico_resumen_diario",
                                    "item as ITEM",
                                    DB::raw("CONCAT(serie_comprobante,'-',correlativo_comprobante) as NRO_COMPROBANTE"),
                                    "id_tipo_comprobante as TIPO_COMPROBANTE",
                                    "numero_documento_cliente as NRO_DOCUMENTO",
                                    "id_tipo_documento_cliente as TIPO_DOCUMENTO",
                                    "id_tipo_comprobante_modificado as TIPO_COMPROBANTE_REF",
                                    DB::raw("CONCAT(serie_comprobante_modificado,'-', correlativo_comprobante_modificado) as NRO_COMPROBANTE_REF"),
                                    "id_tipo_moneda as COD_MONEDA",
                                    "status as STATUS",
                                    "importe_igv as IGV",
                                    "importe_isc as ISC",
                                    "importe_otros as OTROS",
                                    "importe_total as TOTAL",
                                    "importe_gravadas as GRAVADA",
                                    "importe_inafectas as INAFECTO",
                                    "importe_exoneradas as EXONERADO",
                                    "importe_exportacion as EXPORTACION",
                                    "importe_gratuitas as GRATUITAS"
                            );
                        $q->orderBy("item");
                    }
                ])->findOrFail($id, [
                    "id",
                    "codigo as CODIGO",
                    "serie as SERIE",
                    "secuencia as SECUENCIA",
                    "fecha_emision as FECHA_REFERENCIA",
                    "fecha_emision as FECHA_EMISION",
                    "fecha_generacion as FECHA_DOCUMENTO",
                    "nombre_resumen as NOMBRE_RESUMEN",
                    "codigo as COD_TIPO_DOCUMENTO",
                    DB::raw("CONCAT(serie,'-',secuencia) as NRO_COMPROBANTE")
                ]);

        $empresa = EmpresaFacturacion::first([
            "nro_documento_empresa as NRO_DOCUMENTO_EMPRESA",
            "tipo_documento_empresa as TIPO_DOCUMENTO_EMPRESA",
            "nombre_comercial_empresa as NOMBRE_COMERCIAL_EMPRESA",
            "codigo_ubigeo_empresa as CODIGO_UBIGEO_EMPRESA",
            "direccion_empresa as DIRECCION_EMPRESA",
            "departamento_empresa as DEPARTAMENTO_EMPRESA",
            "provincia_empresa as PROVINCIA_EMPRESA",
            "distrito_empresa as DISTRITO_EMPRESA",
            "urbanizacion_empresa as URBANIZACION_EMPRESA",
            "codigo_pais_empresa as CODIGO_PAIS_EMPRESA",
            "razon_social_empresa as RAZON_SOCIAL_EMPRESA",
            "contacto_empresa as CONTACTO_EMPRESA",
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        $rd->NRO_DOCUMENTO_EMPRESA = $empresa->NRO_DOCUMENTO_EMPRESA;
        $rd->TIPO_DOCUMENTO_EMPRESA = $empresa->TIPO_DOCUMENTO_EMPRESA;
        $rd->NOMBRE_COMERCIAL_EMPRESA = $empresa->NOMBRE_COMERCIAL_EMPRESA;
        $rd->CODIGO_UBIGEO_EMPRESA = $empresa->CODIGO_UBIGEO_EMPRESA;
        $rd->DIRECCION_EMPRESA = $empresa->DIRECCION_EMPRESA;
        $rd->DEPARTAMENTO_EMPRESA = $empresa->DEPARTAMENTO_EMPRESA;
        $rd->PROVINCIA_EMPRESA = $empresa->PROVINCIA_EMPRESA;
        $rd->DISTRITO_EMPRESA = $empresa->DISTRITO_EMPRESA;
        $rd->URBANIZACION_EMPRESA = $empresa->URBANIZACION_EMPRESA;
        $rd->RAZON_SOCIAL_EMPRESA = $empresa->RAZON_SOCIAL_EMPRESA;
        $rd->CODIGO_PAIS_EMPRESA = $empresa->CODIGO_PAIS_EMPRESA;
        $rd->CONTACTO_EMPRESA = $empresa->CONTACTO_EMPRESA;
        $rd->EMISOR_RUC = $empresa->EMISOR_RUC;
        $rd->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
        $rd->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;

        $rd->tipo_proceso = $empresa->modo_proceso_emision;

        return  $rd;
    }

    public function generarComprobanteXML(string $id){
        $datosComprobante = $this->obtenerDatosParaCreacionXML($id);
        $data_json = json_encode($datosComprobante);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);

        if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close($ch);

        $fue_generado = 0;
        $xml_filename = NULL;

		if (isset($error_msg)) {
            $respuesta = [];
			$respuesta['respuesta'] = 'error';
			$respuesta['data'] = '';
			$respuesta['mensaje'] = $error_msg;
		} else{
			$respuesta = json_decode($respuesta);
		}

        if (isset($respuesta->respuesta) && $respuesta->respuesta == "ok"){
            $fue_generado = 1;
            $xml_filename = $respuesta->xml_filename;

            DocumentoElectronicoResumenDiario::where(["id"=>$id])
                    ->update([
                        "fue_generado"=>$fue_generado,
                        "xml_filename"=>$respuesta->ruta."/".$respuesta->xml_filename
                    ]);
        }

        return ["respuesta"=>$respuesta,"fue_generado"=>$fue_generado, "datos_comprobante"=>$datosComprobante, "xml_filename"=>$xml_filename];

    }

    private function obtenerDatosParaFirmaXML(string $id){
        $rd = DocumentoElectronicoResumenDiario::findOrFail($id, [
            "nombre_resumen as NOMBRE_RESUMEN",
            "codigo as COD_TIPO_DOCUMENTO",
            DB::raw("CONCAT(serie,'-',secuencia) as NRO_COMPROBANTE"),
            "fecha_emision as FECHA_EMISION",
            "fecha_generacion as FECHA_DOCUMENTO"
        ]);

        $empresa = EmpresaFacturacion::first([
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        $rd->EMISOR_RUC = $empresa->EMISOR_RUC;
        $rd->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
        $rd->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;
        $rd->tipo_proceso = $empresa->modo_proceso_emision;

        return  $rd;
    }

    public function firmarComprobanteXML(string $id){
        $datosComprobante = $this->obtenerDatosParaFirmaXML($id);
        $data_json = json_encode($datosComprobante);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION_FIRMADO);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuestafirma  = curl_exec($ch);

        if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close($ch);

        if (isset($error_msg)) {
            $respuestafirma = (object) [];
			$respuestafirma->respuesta = 'error';
			$respuestafirma->data = '';
			$respuestafirma->mensaje = $error_msg;
		} else{
			$respuestafirma = json_decode($respuestafirma);
		}

        $valor_firma = "";
        $valor_resumen = "";

        if (isset($respuestafirma->respuesta) && $respuestafirma->respuesta == "ok"){
            $valor_firma = $respuestafirma->signature_cpe;
            $valor_resumen = $respuestafirma->hash_cpe;

            DocumentoElectronicoResumenDiario::where(["id"=>$id])
                    ->update([
                        "fue_firmado"=>1,
                        "valor_firma"=>$valor_firma,
                        "valor_resumen"=>$valor_resumen
                    ]);
        }

        return ["respuestafirma"=>$respuestafirma,"valor_firma"=>$valor_firma, "valor_resumen"=>$valor_resumen];
    }
}
