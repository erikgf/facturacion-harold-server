<?php

namespace App\Services;

use App\Models\DocumentoElectronico;
use App\Models\EmpresaFacturacion;
use Illuminate\Support\Facades\DB;

class DocumentoElectronicoXMLService {
    private $RUTA_SISTEMA_FACTURACION;
    private $RUTA_SISTEMA_FACTURACION_FIRMADO;

    function __construct(){
        $API_FACTURACION = config('globals.api_facturacion');
        $this->RUTA_SISTEMA_FACTURACION = $API_FACTURACION."xml.generar.comprobante.php";
        $this->RUTA_SISTEMA_FACTURACION_FIRMADO = $API_FACTURACION."xml.firmar.comprobante.php";
    }

    private function obtenerDatosParaCreacionXML(String $id){
        $doc = DocumentoElectronico::with([
                    "detalle"=>function($q){
                        $q->select("id", "id_documento_electronico",
                                    "item as txtITEM",
                                    "cantidad_item as txtCANTIDAD_DET",
                                    "precio_venta_unitario as txtPRECIO_DET",
                                    "valor_venta_unitario as txtPRECIO_SIN_IGV_DET",
                                    "valor_venta as txtIMPORTE_DET",
                                    DB::raw("IF(total_igv = 0, 0, valor_venta) as txtIMPORTE_DET_IGV"),
                                    "total_igv as txtIGV",
                                    "total_isc as txtISC",
                                    "id_tipo_afectacion as txtCOD_TIPO_OPERACION",
                                    DB::raw("COALESCE(codigo_interno,'') as txtCODIGO_DET"),
                                    "descripcion_item as txtDESCRIPCION_DET",
                                    "id_unidad_medida as txtUNIDAD_MEDIDA_DET",
                                    "id_codigo_precio as txtPRECIO_TIPO_CODIGO"
                            );
                        $q->orderBy("item");
                    },
                    "cuotas"=>function($q){
                        $q->select("id", "id_documento_electronico",
                                    "numero_cuota as NUMERO_CUOTA",
                                    "monto_cuota as MONTO_CUOTA",
                                    "fecha_vencimiento as FECHA_VENCIMIENTO");
                        $q->orderBy("numero_cuota");
                    }
                ])->findOrFail($id, [
                    "id",
                    "id_tipo_afectacion as TIPO_OPERACION",
                    "total_gravadas as TOTAL_GRAVADAS",
                    "total_inafectas as TOTAL_INAFECTA",
                    "total_exoneradas as TOTAL_EXONERADAS",
                    DB::raw("(importe_total - total_igv) as SUB_TOTAL"),
                    "igv as POR_IGV",
                    "total_igv as TOTAL_IGV",
                    "total_isc as TOTAL_ISC",
                    "total_otro_imp as TOTAL_OTR_IMP",
                    "importe_total as TOTAL",
                    "importe_credito as TOTAL_CREDITO",
                    "total_letras as TOTAL_LETRAS",
                    DB::raw("CONCAT(serie,'-', LPAD(correlativo,6,'0')) as NRO_COMPROBANTE"),
                    "fecha_emision as FECHA_DOCUMENTO",
                    "hora_emision as HORA_DOCUMENTO",
                    "fecha_vencimiento as FECHA_VTO",
                    "id_tipo_comprobante as COD_TIPO_DOCUMENTO",
                    "id_tipo_moneda as COD_MONEDA",
                    "numero_documento_cliente as NRO_DOCUMENTO_CLIENTE",
                    "descripcion_cliente as RAZON_SOCIAL_CLIENTE",
                    "id_tipo_documento_cliente as TIPO_DOCUMENTO_CLIENTE",
                    "direccion_cliente as DIRECCION_CLIENTE",
                    DB::raw("'PE' as COD_PAIS_CLIENTE"),
                    "codigo_ubigeo_cliente as COD_UBIGEO_CLIENTE",
                    DB::raw("'' as DEPARTAMENTO_CLIENTE"),
                    DB::raw("'' as PROVINCIA_CLIENTE"),
                    DB::raw("'' as DISTRITO_CLIENTE"),
                    DB::raw("COALESCE(observaciones,'') as OBSERVACIONES"),
                    "condicion_pago as CONDICION_PAGO",
                    DB::raw("''  as NRO_OTR_COMPROBANTE"),
                    DB::raw("''  as NRO_GUIA_REMISION"),
                    DB::raw("'0' as TOTAL_VALOR_VENTA_BRUTO"),
                    DB::raw("'0' as TOTAL_DESCUENTO"),
                    "porcentaje_descuento as POR_DESCUENTO",
                    "descuento_global_igv as DESCUENTO_GLOBAL",
                    "id_tipo_comprobante_modifica as TIPO_COMPROBANTE_MODIFICA",
                    "id_tipo_motivo_nota as COD_TIPO_MOTIVO",
                    "descripcion_motivo_nota as DESCRIPCION_MOTIVO",
                    DB::raw("CONCAT(serie_comprobante_modifica,'-',correlativo_comprobante_modifica) as NRO_DOCUMENTO_MODIFICA"),
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

        $doc->NRO_DOCUMENTO_EMPRESA = $empresa->NRO_DOCUMENTO_EMPRESA;
        $doc->TIPO_DOCUMENTO_EMPRESA = $empresa->TIPO_DOCUMENTO_EMPRESA;
        $doc->NOMBRE_COMERCIAL_EMPRESA = $empresa->NOMBRE_COMERCIAL_EMPRESA;
        $doc->CODIGO_UBIGEO_EMPRESA = $empresa->CODIGO_UBIGEO_EMPRESA;
        $doc->DIRECCION_EMPRESA = $empresa->DIRECCION_EMPRESA;
        $doc->DEPARTAMENTO_EMPRESA = $empresa->DEPARTAMENTO_EMPRESA;
        $doc->PROVINCIA_EMPRESA = $empresa->PROVINCIA_EMPRESA;
        $doc->DISTRITO_EMPRESA = $empresa->DISTRITO_EMPRESA;
        $doc->URBANIZACION_EMPRESA = $empresa->URBANIZACION_EMPRESA;
        $doc->RAZON_SOCIAL_EMPRESA = $empresa->RAZON_SOCIAL_EMPRESA;
        $doc->CODIGO_PAIS_EMPRESA = $empresa->CODIGO_PAIS_EMPRESA;
        $doc->CONTACTO_EMPRESA = $empresa->CONTACTO_EMPRESA;
        $doc->EMISOR_RUC = $empresa->EMISOR_RUC;
        $doc->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
        $doc->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;

        $doc->tipo_proceso = $empresa->modo_proceso_emision;

        return  $doc;
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

            DocumentoElectronico::where(["id"=>$id])
                    ->update([
                        "fue_generado"=>$fue_generado,
                        "xml_filename"=>$respuesta->ruta."/".$respuesta->xml_filename,
                        "cdr_estado"=>NULL
                    ]);
        }

        return ["respuesta"=>$respuesta,"fue_generado"=>$fue_generado, "datos_comprobante"=>$datosComprobante, "xml_filename"=>$xml_filename];

    }

    private function obtenerDatosParaFirmaXML(string $id){
        $doc = DocumentoElectronico::findOrFail($id, [
            DB::raw("CONCAT(serie,'-', LPAD(correlativo,6,'0')) as NRO_COMPROBANTE"),
            "fecha_emision as FECHA_DOCUMENTO",
            "hora_emision as HORA_DOCUMENTO",
            "fecha_vencimiento as FECHA_VTO",
            "id_tipo_comprobante as COD_TIPO_DOCUMENTO",
        ]);

        $empresa = EmpresaFacturacion::first([
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        $doc->EMISOR_RUC = $empresa->EMISOR_RUC;
        $doc->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
        $doc->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;
        $doc->tipo_proceso = $empresa->modo_proceso_emision;

        return  $doc;
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

            DocumentoElectronico::where(["id"=>$id])
                    ->update([
                        "fue_firmado"=>1,
                        "valor_firma"=>$valor_firma,
                        "valor_resumen"=>$valor_resumen
                    ]);
        }

        return ["respuestafirma"=>$respuestafirma,"valor_firma"=>$valor_firma, "valor_resumen"=>$valor_resumen];
    }
}
