<?php

namespace App\Services;

use App\Helper\NumberToLetter;
use App\Models\Cliente;
use App\Models\DocumentoElectronico;
use App\Models\DocumentoElectronicoDetalle;
use App\Models\Producto;
use Illuminate\Support\Str;

class DocumentoElectronicoService {

    private $IGV = 0.18;
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
}
