<?php

namespace App\Services;

use App\Models\DocumentoElectronico;
use App\Models\EmpresaFacturacion;
use App\Models\EnvioComprobanteSunat;
use Illuminate\Support\Facades\DB;

class DocumentoElectronicoSUNATService {
    private $RUTA_SISTEMA_FACTURACION_ENVIAR;

    function __construct(){
        $API_FACTURACION = config('globals.api_facturacion');
        $this->RUTA_SISTEMA_FACTURACION_ENVIAR = $API_FACTURACION."xml.enviar.comprobante.facturas.php";
    }

    private function obtenerDatosParaEnviar(string $id){
        $doc =  DocumentoElectronico::where([
                        "id"=>$id,
                        "fue_firmado"=>"1",
                        "fue_generado"=>"1"
                    ])
                    ->whereNull("cdr_estado")
                    ->where(["esta_anulado"=>"0", "enviar_a_sunat"=>"0"])
                    ->select(
                        "id", "xml_filename as nombre_archivo", "fecha_emision","id_tipo_comprobante"
                    )->firstOrFail();

        $empresa = EmpresaFacturacion::first([
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        $doc->EMISOR_RUC = $empresa->EMISOR_RUC;
        $doc->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
        $doc->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;

        return ["comprobantes"=>[$doc], "id_tipo_comprobante"=>$doc->id_tipo_comprobante, "tipo_proceso"=> $empresa->modo_proceso_emision];
    }

    public function enviarComprobante(string $id){
        $datosComprobante = $this->obtenerDatosParaEnviar($id);
        $data_json = json_encode($datosComprobante);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION_ENVIAR);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $respuestasEnvioJSON  = curl_exec($ch);

        if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}

        if (!json_validate($respuestasEnvioJSON)){
            $error_msg = $respuestasEnvioJSON;
        }

		curl_close($ch);

        if (isset($error_msg)) {
            $respuestasEnvio = (object) [];
			$respuestasEnvio->respuesta = 'error';
			$respuestasEnvio->data = '';
			$respuestasEnvio->mensaje = $error_msg;
            return $respuestasEnvio;
		}

		$respuestasEnvio = json_decode($respuestasEnvioJSON);

        if ($respuestasEnvio){
            foreach ($respuestasEnvio as $key => $respuesta) {
                $respuestaOk = isset($respuesta->respuesta) && $respuesta->respuesta === "ok";
                $numeroEnvios = EnvioComprobanteSunat::where([
                        "id_comprobante_asociado"=>$respuesta->id,
                        "id_tipo_comprobante"=>$respuesta->id_tipo_comprobante
                    ])->max("item") + 1;

                $envioSunat = new EnvioComprobanteSunat;
                $envioSunat->id_comprobante_asociado = $respuesta->id;
                $envioSunat->id_tipo_comprobante = $respuesta->id_tipo_comprobante;
                $envioSunat->item = $numeroEnvios;
                $envioSunat->nombre_comprobante = $respuesta->nombre_archivo;
                $envioSunat->fecha_hora_envio = now();
                $envioSunat->cdr_estado = $respuesta->cod_sunat;
                $envioSunat->cdr_descripcion =$respuesta->mensaje;
                $envioSunat->hash_cdr = $respuesta->hash_cdr;
                $envioSunat->save();

                if ($respuestaOk){
                    $codSunat = $respuesta->cod_sunat;
                    $enviar_a_sunat = "1";
                    $cdr_descripcion  = $respuesta->mensaje;
                    $fue_anulado_por_nota = "1";
                    $estado_anulado = "0";

                    if ($codSunat < 0){
                        $enviar_a_sunat = "0";
                        $cdr_descripcion = "ERROR POR NO CONEXION A SUNAT. REENVIAR XML NUEVAMENTE.";
                    } else {
                        if ($codSunat >= 2000){
                            $enviar_a_sunat = 1;
                            $estado_anulado = "1";
                            $fue_anulado_por_nota = "0";
                            $cdr_descripcion = "RECHAZADO SUNAT: ".$respuesta->mensaje;
                        } else if ($codSunat > 0 && $codSunat < 2000) {
                            $enviar_a_sunat = 0;
                            $cdr_descripcion = "ERROR POR EXCEPCION. GENERAR O REENVIAR XML NUEVAMENTE.";
                        }
                    }

                    DocumentoElectronico::where(["id"=>$respuesta->id])
                        ->update([
                            "enviar_a_sunat"=>$enviar_a_sunat,
                            "cdr_estado"=>$codSunat,
                            "cdr_descripcion"=>$cdr_descripcion,
                            "cdr_hash"=>$respuesta->hash_cdr,
                            "cdr_filename"=>$respuesta->xml_cdr,
                            "fue_anulado_por_nota"=>$fue_anulado_por_nota,
                            "esta_anulado"=>$estado_anulado
                        ]);
                }
            }
        }
        return $respuestasEnvio;
    }

}
