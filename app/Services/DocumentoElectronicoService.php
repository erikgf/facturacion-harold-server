<?php

namespace App\Services;

use App\Helper\NumberToLetter;
use App\Models\Cliente;
use App\Models\DocumentoElectronico;
use App\Models\DocumentoElectronicoDetalle;
use App\Models\Producto;
use App\Models\SerieDocumento;
use Illuminate\Support\Str;

class DocumentoElectronicoService {
    private $IGV = 0.18;

    public function registrar(array $data){
        $esCorrelativoAutomatico = false;
        $doc = new DocumentoElectronico();

        $doc->id_tipo_comprobante =  $data['id_tipo_comprobante'];
        $doc->serie = $data["serie"];
        $correlativo = @$data["correlativo"] ?: NULL;

        if ($correlativo == NULL){
            $correlativo = SerieDocumento::where(["serie" => $data["serie"], "id_tipo_comprobante"=>$data["id_tipo_comprobante"]])->pluck("correlativo")->first();
            if ($correlativo == NULL){
                throw new \Exception("Correlativo de serie ".$data["serie"]. " no encontrado.", 1);
            }
            $esCorrelativoAutomatico = true;
        }

        $doc->correlativo = $correlativo;

        if (!$esCorrelativoAutomatico){
            $existeRepetido = DocumentoElectronico::where([
                "id_tipo_comprobante"=>$doc->id_tipo_comprobgante,
                "serie"=>$doc->serie,
                "correlativo"=>$doc->correlativo
            ])->exists();

            if ($existeRepetido){
                throw new \Exception("El comprobante electrónico a registrar ya existe.", 1);
            }
        }

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
        $doc->hora_emision = $data["hora_emision"].":00";
        $doc->id_tipo_moneda = $data["id_tipo_moneda"];

        $doc->importe_credito = @$data["monto_credito"] ?: 0.00;

        $data["descuento_global"] = @$data["descuento_global"] ?: 0.00;

        $esDocumentoElectronicoCredito = $doc->importe_credito > 0.00;
        $esDocumentoElectronicoDescuento =  $data['descuento_global'] > 0.00;
        $noEsDocumentoElectronicoNota = in_array($data["id_tipo_comprobante"], ["01","03"]);

        $doc->total_inafectas = 0.00;
        $doc->total_exoneradas = 0.00;

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
            //Debo chequear si el comprobante al que le aplico existe en SUNAT.
            $docMod = DocumentoElectronico::where([
                "id_tipo_comprobante"=>$data["id_tipo_comprobante_modifica"],
                "serie"=>$data["serie_comprobante_modifica"],
                "correlativo"=>$data["correlativo_comprobante_modifica"],
                "estado_anulado"=>"0",
                "cdr_estado"=>"0"
            ])->first();

            if ($docMod == NULL){
                throw new \Exception("No puedo emitir una nota a un comprobante que NO está en SUNAT.", 1);
            }

            $esDocumentoElectronicoNotaCreditoAnuladora = in_array($data["id_tipo_comprobante"], ["07"]) &&
                                                                in_array($data["id_tipo_motivo_nota"], ["01","02","03"]);

            if ($esDocumentoElectronicoNotaCreditoAnuladora){
                /* Marcar como anulado */
                $docMod->fue_anulado_por_nota = 1;
                $docMod->estado_anulado = 1;
                $docMod->update();
            }

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
            $valorVentaUnitario = round($productoDetalle["precio_unitario"] / (1 + $this->IGV), 2);
            $valorVenta =  $valorVentaUnitario * $productoDetalle["cantidad"];
            $totalIgv = $subTotal - $valorVenta;

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
            $docDetalle->valor_venta = $valorVenta;
            $docDetalle->total_igv = $totalIgv;
            $docDetalle->id_tipo_afectacion = "10";
            $docDetalle->id_codigo_precio = "01";
            $docDetalle->codigo_interno = Str::padLeft($productoDetalle["id_producto"], 10, '0');

            $docDetalle->save();
        }

        SerieDocumento::where([
            "id_tipo_comprobante"=>$doc->id_tipo_comprobante,
            "serie"=>$doc->serie
        ])->update([
            "correlativo"=>$doc->correlativo + 1
        ]);

        return $doc;
    }

    public function anular(DocumentoElectronico $doc){
        $esNota = in_array($doc->id_tipo_comprobante, ["07","08"]);
        $esComprobante = in_array($doc->id_tipo_comprobante, ["01","03"]);
        $esSunat = $doc->cdr_estado === "0";

        if ($esComprobante){
            if ($esSunat){
                throw new \Exception("No puedo eliminar el comprobante ".$doc->serie."-".$doc->correlativo.", ya está en SUNAT. Por favor emita una NOTA DE CRÉDITO.", 1);
            }
        }

        if ($esNota){
            if ($esSunat){
                throw new \Exception("No puedo eliminar el comprobante ".$doc->serie."-".$doc->correlativo.", ya está en SUNAT. No puedo anularlo.", 1);
            }

            if (in_array($doc->id_tipo_comprobante, ["07"]) &&
                in_array($doc->id_tipo_motivo_nota, ["01","02","03"])){
                    DocumentoElectronico::where([
                        "id_tipo_comprobante"=>$doc->id_tipo_comprobante_modifica,
                        "serie"=>$doc->serie_comprobante_modifica,
                        "correlativo"=>$doc->correlativo_comprobante_modifica
                    ])->update([
                        "estado_anulado"=>"0"
                    ]);
            }

        }

        $doc->delete();
        return $doc;
    }
}
