<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\SucursalProducto;
use App\Models\SucursalProductoHistorial;
use App\Models\VentaCredito;
use Database\Seeders\DocumentoElectronicoSeeder;

class VentaService {

    public function registrar(array $data){
        $venta = new Venta();

        $venta->id_tipo_comprobante =  $data['id_tipo_comprobante'];
        $venta->serie = $data["serie"];
        $venta->correlativo = $data["correlativo"];

        $idCliente = @$data['id_cliente']?: NULL;

        if (is_null($idCliente)){
            $cliente = new Cliente;
            $cliente->id_tipo_documento = $data["cliente_id_tipo_documento"];
            $cliente->numero_documento = $data["cliente_numero_documento"];
            $cliente->nombres = $data["cliente_nombres"];
            $cliente->apellidos = $data["cliente_apellidos"];
            $cliente->direccion = $data["cliente_direccion"];
            $cliente->correo = $data["cliente_correo"];
            $cliente->celular = $data["cliente_celular"];
            $cliente->save();

            $data["id_cliente"] = $cliente->id;
        }

        $venta->id_cliente =  $data['id_cliente'];

        $venta->observaciones = @$data['observaciones']?: NULL;

        $venta->fecha_venta = $data["fecha_venta"];
        $venta->hora_venta = $data["hora_venta"];

        $venta->monto_efectivo = $data['monto_efectivo'];
        $venta->monto_tarjeta = $data['monto_tarjeta'];
        $venta->monto_credito = $data['monto_credito'];
        $venta->monto_yape = $data['monto_yape'];
        $venta->monto_plin = $data['monto_plin'];
        $venta->monto_transferencia = $data['monto_transferencia'];

        $esVentaCredito = $data['monto_credito'] > 0.00;

        $venta->tipo_pago =  $esVentaCredito ? 'R' : 'C';

        $venta->tipo_descuento = "V";
        $venta->valor_descuento = $data['descuento_global'];
        $venta->monto_descuento = $data['descuento_global'];

        $data["id_tipo_moneda"] = "PEN";
        $venta->id_tipo_moneda = $data["id_tipo_moneda"];

        $venta->sub_total = $data['importe_total'] + $data["descuento_global"];
        $venta->monto_total_venta = $data['importe_total'];
        $venta->id_sucursal = $data['id_sucursal'];
        $venta->id_usuario_registro =  $data["id_usuario_registro"];

        $venta->save();

        foreach ($data["productos"] as $i => $productoDetalle) {
            $item = $i + 1;
            $objProducto = Producto::find($productoDetalle["id_producto"]);
            if (!$objProducto){
                throw new \Exception("Producto no existe en el sistema.", 1);
            }

            if ($productoDetalle["cantidad"] <= 0){
                throw new \Exception("Producto de la fila ".($item)." no tiene cantidad válida.", 1);
            }

            $fechaVencimiento = isset($productoDetalle["fecha_vencimiento"]) ? $productoDetalle["fecha_vencimiento"] : '0000-00-00';
            $lote = isset($productoDetalle["lote"]) ? $productoDetalle["lote"] : "";

            $stockActual = SucursalProducto::where([
                                    "id_producto"=>$productoDetalle["id_producto"],
                                    "id_sucursal"=>$data["id_sucursal"],
                                    "lote"=>$lote,
                                    "fecha_vencimiento"=>$fechaVencimiento
                                ])
                                ->sum("stock");

            if ($productoDetalle["cantidad"] > $stockActual){
                throw new \Exception("Producto de fila ".($item). " no tiene stock suficiente.", 1);
            }

            /*ACTUALIZAMOS SUCURSAL*/
            /*
            if ($stockActual == null){
                $sucursalProducto = new SucursalProducto;
                $sucursalProducto->id_sucursal = $data["id_sucursal"];
                $sucursalProducto->id_producto = $productoDetalle["id_producto"];
                $sucursalProducto->precio_entrada = $productoDetalle["precio_unitario"];
                $sucursalProducto->stock = $productoDetalle["cantidad"];
                $sucursalProducto->lote = $lote;
                $sucursalProducto->fecha_vencimiento = $fechaVencimiento;
                $sucursalProducto->save();
            } else {
                SucursalProducto::where([
                    "id_producto"=>$productoDetalle["id_producto"],
                    "id_sucursal"=>$data["id_sucursal"],
                    "precio_entrada"=>$productoDetalle["precio_unitario"],
                    "fecha_vencimiento"=>$fechaVencimiento,
                    "lote"=>$lote
                ])->increment('stock', $productoDetalle["cantidad"]);
            }
            */

            $sucursalProductoHistorial = new SucursalProductoHistorial;
            $sucursalProductoHistorial->id_producto = $productoDetalle["id_producto"];
            $sucursalProductoHistorial->id_sucursal_origen = $data["id_sucursal"];
            $sucursalProductoHistorial->precio_salida = $productoDetalle["precio_unitario"];
            $sucursalProductoHistorial->cantidad = $productoDetalle["cantidad"];
            $sucursalProductoHistorial->id_venta = $venta->id;
            $sucursalProductoHistorial->fecha_movimiento = $venta["fecha_venta"];
            $sucursalProductoHistorial->tipo_movimiento = 'S';
            $sucursalProductoHistorial->fecha_vencimiento = $fechaVencimiento;
            $sucursalProductoHistorial->lote = $lote;
            $sucursalProductoHistorial->save();

            $arregloStocks = SucursalProducto::where([
                                    "id_producto"=>$productoDetalle["id_producto"],
                                    "id_sucursal"=>$data["id_sucursal"],
                                    "fecha_vencimiento"=>$fechaVencimiento,
                                    "lote"=>$lote
                                ])
                                ->orderBy("stock", "desc")
                                ->select("precio_entrada", "stock")
                                ->get();

            $restante = $productoDetalle["cantidad"];

            $cadenaStock = "[";
            foreach ($arregloStocks as $stockItem) {
                $stock = $stockItem["stock"];
                if ($restante > $stock){
                    $restante = $restante - $stock;
                    $consumido = $stock;
                } else {
                    $consumido = $restante;
                    $restante = 0;
                }

                SucursalProducto::where([
                        "id_producto"=>$productoDetalle["id_producto"],
                        "id_sucursal"=>$data["id_sucursal"],
                        "fecha_vencimiento"=>$fechaVencimiento,
                        "lote"=>$lote,
                        "precio_entrada"=>$stockItem["precio_entrada"]
                    ])->decrement('stock', $consumido);

                $cadenaStock .= '{"cantidad":"'.$consumido.'","precio_entrada":"'.$stockItem["precio_entrada"].'"}';
                if ($restante <= 0){
                    break;
                }
            }
            $cadenaStock .= "]";

            $subTotal = round($productoDetalle["precio_unitario"] * $productoDetalle["cantidad"], 3);

            $costoProducto  = 0;
            $arregloStocksDecoded = json_decode($cadenaStock);
            foreach ($arregloStocksDecoded as $__key => $__value) {
                $costoProducto += ($__value->cantidad  * $__value->precio_entrada);
            }

            $ventaDetalle = new VentaDetalle;
            $ventaDetalle->id_venta = $venta->id;
            $ventaDetalle->item = $item;
            $ventaDetalle->id_producto = $productoDetalle["id_producto"];
            $ventaDetalle->fecha_vencimiento = $fechaVencimiento;
            $ventaDetalle->lote = $lote;
            $ventaDetalle->cantidad = $productoDetalle["cantidad"];
            $ventaDetalle->descripcion_producto = $objProducto->nombre;
            $ventaDetalle->precio_venta_unitario = $productoDetalle["precio_unitario"];
            $ventaDetalle->subtotal = $subTotal;
            $ventaDetalle->cadena_stock_producto = $cadenaStock;
            $ventaDetalle->costo_producto = $costoProducto;
            $ventaDetalle->id_unidad_medida = $objProducto->id_unidad_medida;
            $ventaDetalle->precio_venta_unitario = $productoDetalle["precio_unitario"];

            $ventaDetalle->save();
        }

        if ($esVentaCredito){
            $ventaCredito = new VentaCredito;
            $ventaCredito->id_venta = $venta->id;
            $ventaCredito->monto = $data["monto_credito"];
            $ventaCredito->tipo_deuda = -1;
            $ventaCredito->fecha_registro = $data["fecha_venta"];
            $ventaCredito->pendiente = $data["monto_credito"];
            $ventaCredito->save();
        }

        if (in_array($data["id_tipo_comprobante"], ['01','03'])){
            $data["fecha_emision"] = $data["fecha_venta"];
            $data["hora_emision"] = $data["hora_venta"];
            $data["id_atencion"] = $venta->id;
            $doc = (new DocumentoElectronicoService())->registrar($data);
            $venta->id_documento_electronico = $doc->id;
        }

        return $venta;
    }

    public function anular(Venta $venta){
        $venta->load(["detalle", "credito", "pagos"]);

        if (count($venta->pagos) > 0){
            throw new \Exception("Existen pagos realizados a esta venta, eliminar primero los pagos.", 1);
        }

        if ($venta->credito){
            $venta->credito->delete();
        }

        foreach ($venta->detalle as $key => $detalle) {
            $arregloStock = json_decode($detalle->cadena_stock_producto);
            if ($arregloStock == NULL){
                continue;
            }
            foreach ($arregloStock as $j => $stock) {
                SucursalProducto::where([
                    "id_producto"=>$detalle->id_producto,
                    "precio_entrada"=>$stock->precio_entrada,
                    "id_sucursal"=>$venta->id_sucursal,
                    "fecha_vencimiento"=>$detalle->fecha_vencimiento,
                    "lote"=>$detalle->lote
                ])
                ->increment("stock", $stock->cantidad);
            }

        }

        SucursalProductoHistorial::where(["id_venta"=>$venta->id])->delete();
        $venta->delete();

        return $venta;
    }

}
