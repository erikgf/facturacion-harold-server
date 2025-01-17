<?php

namespace App\Services;

use App\Models\DocumentoElectronico;
use App\Models\DocumentoElectronicoResumenDiario;
use App\Models\DocumentoElectronicoResumenDiarioDetalle;
use App\Models\EmpresaFacturacion;
use App\Models\EnvioComprobanteSunat;
use Illuminate\Support\Facades\DB;

class DocumentoElectronicoResumenDiarioSUNATService {
    private $CODIGO_COMPROBANTE = "RC";
    private $RUTA_SISTEMA_FACTURACION_ENVIAR;
    private $RUTA_SISTEMA_FACTURACION_CONSULTAR_TICKET_RD;

    function __construct(){
        $API_FACTURACION = config('globals.api_facturacion');
        $this->RUTA_SISTEMA_FACTURACION_ENVIAR = $API_FACTURACION."xml.enviar.comprobante.resumen.php";
        $this->RUTA_SISTEMA_FACTURACION_CONSULTAR_TICKET_RD = $API_FACTURACION."xml.consultar.ticket.rd.php";
    }

    private function obtenerDatosParaEnviar(string $id){
        $rd =  DocumentoElectronicoResumenDiario::where([
                        "id"=>$id,
                        "fue_firmado"=>"1",
                        "fue_generado"=>"1"
                    ])
                    ->whereNull("ticket")
                    ->select(
                        "id", "nombre_resumen as nombre_archivo", "fecha_emision"
                    )->firstOrFail();

        $empresa = EmpresaFacturacion::first([
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        $rd->EMISOR_RUC = $empresa->EMISOR_RUC;
        $rd->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
        $rd->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;

        return ["resumenes"=>[$rd], "id_tipo_comprobante"=>$this->CODIGO_COMPROBANTE, "tipo_proceso"=> $empresa->modo_proceso_emision];
    }

    public function obtenerDatosParaEnviarMasivo(string $fechaDesde, string $fechaHasta){
        $rds =  DocumentoElectronicoResumenDiario::query()
                    ->where([
                        "fue_firmado"=>"1",
                        "fue_generado"=>"1"
                    ])
                    ->whereBetween("fecha_emision", [$fechaDesde, $fechaHasta])
                    ->whereNull("ticket")
                    ->select(
                        "id", "nombre_resumen as nombre_archivo", "fecha_emision"
                    )
                    ->get();

        $empresa = EmpresaFacturacion::first([
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        foreach ($rds as $rd) {
            $rd->EMISOR_RUC = $empresa->EMISOR_RUC;
            $rd->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
            $rd->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;
        }

        return ["resumenes"=>$rds->toArray(), "id_tipo_comprobante"=>$this->CODIGO_COMPROBANTE, "tipo_proceso"=> $empresa->modo_proceso_emision];
    }

    public function enviarComprobante(string $id){
        $datosComprobante = $this->obtenerDatosParaEnviar($id);
        return $this->enviarComprobantes($datosComprobante);
    }

    public function enviarComprobantes(mixed $datosComprobante){
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
            foreach ($respuestasEnvio as $respuesta) {
                $ticket = NULL;

                if (isset($respuesta->respuesta) && $respuesta->respuesta == "ok"){
                    $ticket = $respuesta->cod_ticket;
                }

                $numeroEnvios = EnvioComprobanteSunat::where([
                        "id_comprobante_asociado"=>$respuesta->id,
                        "id_tipo_comprobante"=>$this->CODIGO_COMPROBANTE
                    ])->max("item") + 1;

                $envioSunat = new EnvioComprobanteSunat;
                $envioSunat->id_comprobante_asociado = $respuesta->id;
                $envioSunat->id_tipo_comprobante = $this->CODIGO_COMPROBANTE;
                $envioSunat->item = $numeroEnvios;
                $envioSunat->nombre_comprobante = $respuesta->nombre_archivo;
                $envioSunat->fecha_hora_envio = now();
                $envioSunat->ticket = $ticket;
                $envioSunat->save();

                if ($ticket){
                    DocumentoElectronicoResumenDiario::where(["id"=>$respuesta->id])
                        ->update([
                            "ticket"=>$ticket,
                            "enviar_a_sunat"=>($ticket ? '1' : '0'),
                            "numero_envios"=>$numeroEnvios
                        ]);
                }
            }
        }
        return $respuestasEnvio;
    }

    private function obtenerDatosParaConsultarTicket(string $id){
        $rd =  DocumentoElectronicoResumenDiario::where([
                        "id"=>$id
                    ])
                    ->whereNotNull("ticket")
                    ->select(
                        "id", "nombre_resumen", "fecha_emision", "ticket"
                    )->firstOrFail();

        $empresa = EmpresaFacturacion::first([
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        $rd->EMISOR_RUC = $empresa->EMISOR_RUC;
        $rd->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
        $rd->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;

        return ["tickets"=>[$rd], "id_tipo_comprobante"=>$this->CODIGO_COMPROBANTE, "tipo_proceso"=> $empresa->modo_proceso_emision];
    }

    public function obtenerDatosParaConsultarTicketMasivo(string $fechaDesde, string $fechaHasta){
        $rds =  DocumentoElectronicoResumenDiario::query()
                    ->whereBetween("fecha_emision", [$fechaDesde, $fechaHasta])
                    ->whereNotNull("ticket")
                    ->select(
                        "id", "nombre_resumen", "fecha_emision", "ticket"
                    )
                    ->get();

        $empresa = EmpresaFacturacion::first([
            "emisor_ruc as EMISOR_RUC",
            "emisor_usuario_sol as EMISOR_USUARIO_SOL",
            "emisor_pass_sol as EMISOR_PASS_SOL",
            "modo_proceso_emision"
        ]);

        foreach ($rds as $rd) {
            $rd->EMISOR_RUC = $empresa->EMISOR_RUC;
            $rd->EMISOR_USUARIO_SOL = $empresa->EMISOR_USUARIO_SOL;
            $rd->EMISOR_PASS_SOL = $empresa->EMISOR_PASS_SOL;
        }

        return ["tickets"=>$rds->toArray(), "id_tipo_comprobante"=>$this->CODIGO_COMPROBANTE, "tipo_proceso"=> $empresa->modo_proceso_emision];
    }

    public function consultarTicket(string $id){
        $datosComprobante = $this->obtenerDatosParaConsultarTicket($id);
        return $this->consultarTickets($datosComprobante);
    }

    public function consultarTickets(mixed $datosComprobante){
        $data_json = json_encode($datosComprobante);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION_CONSULTAR_TICKET_RD);
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

        foreach ($respuestasEnvio as $key => $respuesta) {
            $cod_sunat = NULL;
            $msj_sunat = NULL;
            $hash_cdr = NULL;

            if (isset($respuesta->respuesta) && $respuesta->respuesta == "ok"){
                $cod_sunat = $respuesta->cod_sunat;
                $msj_sunat = $respuesta->msj_sunat;
                $hash_cdr = $respuesta->hash_cdr;
            } else {
                $cod_sunat = $respuesta->cod_sunat;
                $msj_sunat = $respuesta->mensaje;
            }

            $numeroEnvios = EnvioComprobanteSunat::where([
                "id_comprobante_asociado"=>$respuesta->id,
                "id_tipo_comprobante"=>$this->CODIGO_COMPROBANTE
            ])->max("item") + 1;

            $envioSunat = new EnvioComprobanteSunat;
            $envioSunat->id_comprobante_asociado = $respuesta->id;
            $envioSunat->id_tipo_comprobante = $this->CODIGO_COMPROBANTE;
            $envioSunat->item = $numeroEnvios;
            $envioSunat->nombre_comprobante = $respuesta->nombre_archivo;
            $envioSunat->fecha_hora_envio = now();
            $envioSunat->cdr_estado = isset($cod_sunat) ? $cod_sunat : NULL;
            $envioSunat->cdr_descripcion = isset($msj_sunat) ? str_replace("'","\'",$msj_sunat) : NULL;
            $envioSunat->hash_cdr = isset($hash_cdr) ? $hash_cdr : NULL;

            $envioSunat->save();

            DocumentoElectronicoResumenDiario::where(["id"=>$respuesta->id])
                ->update([
                    "cdr_estado"=>$envioSunat->cdr_estado,
                    "cdr_descripcion"=>$envioSunat->cdr_descripcion,
                    "hash_cdr"=>$envioSunat->hash_cdr
                ]);

            if (isset($cod_sunat)){
                if ($cod_sunat === "0"){

                    $idRD = $respuesta->id;
                    DocumentoElectronico::whereIn('id', function($query) use($idRD){
                        $query->select('id_documento_electronico')
                            ->from(with(new DocumentoElectronicoResumenDiarioDetalle)->getTable())
                            ->where('id_documento_electronico_resumen_diario', $idRD);
                    })->update([
                        'cdr_estado' => '0',
                        'enviar_a_sunat'=>'1',
                        "cdr_descripcion" => DB::raw("CONCAT('La ',
                            CASE
                            WHEN id_tipo_comprobante = '03' THEN CONCAT('Boleta de Venta número ', serie,'-',LPAD(correlativo,6,'0'))
                            WHEN id_tipo_comprobante = '07' THEN CONCAT('Nota de Crédito número', serie,'-',LPAD(correlativo,6,'0'))
                            ELSE CONCAT('Nota de Débito número', serie,'-',LPAD(correlativo,6,'0'))
                            END, ', ha sido aceptada')"
                        )
                    ]);
                }
            }
        }

        return $respuestasEnvio;
    }
}
