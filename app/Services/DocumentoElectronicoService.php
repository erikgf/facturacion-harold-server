<?php

namespace App\Services;

use App\Helper\NumberToLetter;
use App\Models\Cliente;
use App\Models\DocumentoElectronico;
use App\Models\DocumentoElectronicoDetalle;
use App\Models\EmpresaFacturacion;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Cast\Object_;

class DocumentoElectronicoService {

    private $IGV = 0.18;
    private $RUTA_SISTEMA_FACTURACION = "http://localhost/andreitababy-facturacion/api/xml.generar.comprobante.php";
    private $RUTA_SISTEMA_FACTURACION_FIRMADO = "http://localhost/andreitababy-facturacion/api/xml.firmar.comprobante.php";
    //private $RUTA_SISTEMA_FACTURACION_ENVIAR = "http://localhost/andreitababy-facturacion/api/xml.enviar.comprobante.facturas.php";

    public function registrar(array $data){
        $doc = new DocumentoElectronico();

        $doc->id_tipo_comprobante =  $data['id_tipo_comprobante'];
        $doc->serie = $data["serie"];
        $doc->correlativo = $data["correlativo"];
        $doc->id_cliente =  $data['id_cliente'];

        $cliente = Cliente::findOrFail($data["id_cliente"], ["id_tipo_documento",
            "numero_documento", "nombres", "apellidos", "direccion"
        ]);

        $doc->id_tipo_documento_cliente = $cliente->id_tipo_documento;
        $doc->numero_documento_cliente = $cliente->numero_documento;
        $doc->descripcion_cliente = $cliente->nombres.' '.$cliente->apellidos;
        $doc->direccion_cliente = $cliente->direccion;

        $doc->fecha_emision = $data["fecha_emision"];
        $doc->fecha_vencimiento = @$data["fecha_vencimiento"] ?: $data["fecha_emision"];
        $doc->hora_emision = $data["hora_venta"];
        $doc->id_tipo_moneda = $data["id_tipo_moneda"];

        $esDocumentoElectronicoCredito = $data['monto_credito'] > 0.00;
        $esDocumentoElectronicoDescuento =  $data['descuento_global'] > 0.00;
        $noEsDocumentoElectronicoNota = in_array($data["id_tipo_comprobante"], ["01","03"]);

        $doc->total_inafectas = 0.00;
        $doc->total_exoneradas = 0.00;
        $doc->importe_credito = $data["monto_credito"];

        if ($esDocumentoElectronicoDescuento){
            $doc->descuento_global =  $data['descuento_global'];
            $doc->descuento_global_igv = round($data["descuento_global"] / (1 + $this->IGV), 2);
            $doc->porcentaje_descuento = ($doc->descuento_global_igv / ($doc->descuento_global_igv + $data["importe_total"])) * 100;
        }

        $doc->importe_total = round($data["importe_total"], 2);
        $doc->igv = round($this->IGV * 100, 2);
        $doc->total_gravadas = round($data["importe_total"] / (1 + $this->IGV),2);
        $doc->total_igv = $doc->importe_total - $doc->total_gravadas;

        $doc->total_letras = NumberToLetter::convertir($data["importe_total"], $data["id_tipo_moneda"]);

        if (!$noEsDocumentoElectronicoNota){
            $doc->id_tipo_comprobante_modifica = $data["id_tipo_comprobante_modifica"];
            $doc->serie_comprobante_modifica = $data["serie_comprobante_modifica"];
            $doc->correlativo_comprobante_modifica = $data["correlativo_comprobante_modifica"];
            $doc->id_tipo_motivo_nota = $data["id_tipo_motivo_nota"];
            $doc->descripcion_motivo_nota = $data["descripcion_motivo_nota"];
        }

        $doc->observaciones = @$data['observaciones']?: null;
        $doc->condicion_pago =  $esDocumentoElectronicoCredito ? '0' : '1';
        $doc->id_atencion = @$data['id_atencion']?: null;
        $doc->es_delivery = @$data['es_delivery']?: 0;

        $doc->id_usuario_registro = $data["id_usuario_registro"];

        $doc->save();

        foreach ($data["productos"] as $i => $productoDetalle) {
            $item = $i + 1;
            $objProducto = Producto::find($productoDetalle["id_producto"]);
            if (!$objProducto){
                throw new \Exception("Producto no existe en el sistema.", 1);
            }

            if ($productoDetalle["cantidad"] <= 0){
                throw new \Exception("Producto de la fila ".($item)." no tiene cantidad válida.", 1);
            }

            $subTotal = round($productoDetalle["precio_unitario"] * $productoDetalle["cantidad"], 3);
            $valorVentaUnitario = round($subTotal / (1 + $this->IGV), 2);
            $totalIgv = $subTotal - $valorVentaUnitario;

            $docDetalle = new DocumentoElectronicoDetalle;
            $docDetalle->id_documento_electronico = $doc->id;
            $docDetalle->item = $item;
            $docDetalle->id_producto = $productoDetalle["id_producto"];
            $docDetalle->id_unidad_medida = $objProducto->id_unidad_medida;
            $docDetalle->cantidad_item = $productoDetalle["cantidad"];
            $docDetalle->descripcion_item = $objProducto->nombre;
            $docDetalle->precio_venta_unitario = $productoDetalle["precio_unitario"];
            $docDetalle->subtotal = $subTotal;
            $docDetalle->valor_venta_unitario = $valorVentaUnitario;
            $docDetalle->total_igv = $totalIgv;
            $docDetalle->id_tipo_afectacion = "10";
            $docDetalle->id_codigo_precio = "01";
            $docDetalle->codigo_interno = Str::padLeft($productoDetalle["id_producto"], 10, '0');

            $docDetalle->save();
        }

        return $doc;
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
                        "xml_filename"=>$respuesta->ruta."/".$respuesta->xml_filename
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
