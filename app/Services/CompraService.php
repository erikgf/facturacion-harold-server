<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\SucursalProducto;
use App\Models\SucursalProductoHistorial;
use Illuminate\Http\Response;

class CompraService {

    public function registrar(array $data){

        $compra = new Compra();

        $compra->numero_comprobante =  $data['numero_comprobante'];
        $compra->id_tipo_comprobante =  $data['id_tipo_comprobante'];
        $compra->id_proveedor =  $data['id_proveedor'];

        $compra->tipo_pago = $data['tipo_pago'];
        $compra->tipo_tarjeta = @$data['tipo_tarjeta']?: null;
        $compra->observaciones = @$data['observaciones']?: null;
        $compra->guias_remision = @$data['guias_remision']?: null;

        $compra->fecha_compra = $data["fecha_compra"];
        $compra->hora_compra = $data["hora_compra"];
        $compra->importe_total = $data['importe_total'];
        $compra->id_sucursal = $data['id_sucursal'];
        $compra->id_usuario_registro = $data["id_usuario_registro"];

        $compra->save();

        foreach ($data["productos"] as $i => $productoDetalle) {
            $item = $i + 1;
            $objProducto = Producto::find($productoDetalle["id_producto"]);
            if (!$objProducto){
                abort(Response::HTTP_NOT_FOUND, "Producto no existe en el sistema.");
            }

            if ($productoDetalle["cantidad"] <= 0){
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, "Producto de la fila ".($item)." no tiene cantidad válida.");
            }

            $fechaVencimiento = isset($productoDetalle["fecha_vencimiento"]) ? $productoDetalle["fecha_vencimiento"] : '0000-00-00';
            $lote = isset($productoDetalle["lote"]) ? $productoDetalle["lote"] : "";

            $existeStock = SucursalProducto::where([
                                    "id_producto"=>$productoDetalle["id_producto"],
                                    "id_sucursal"=>$data["id_sucursal"],
                                    "precio_entrada"=>$productoDetalle["precio_unitario"],
                                    "lote"=>$lote,
                                    "fecha_vencimiento"=>$fechaVencimiento
                                ])
                                ->exists();

            //var_dump($existeStock, $productoDetalle, $lote, $fechaVencimiento);

            /*ACTUALIZAMOS SUCURSAL*/
            if (!$existeStock){
                /*No existe sucursal_producto, deberíamos ingresar*/
                $sucursalProducto = new SucursalProducto;
                $sucursalProducto->id_sucursal = $data["id_sucursal"];
                $sucursalProducto->id_producto = $productoDetalle["id_producto"];
                $sucursalProducto->precio_entrada = $productoDetalle["precio_unitario"];
                $sucursalProducto->stock = $productoDetalle["cantidad"];
                $sucursalProducto->lote = $lote;
                $sucursalProducto->fecha_vencimiento = $fechaVencimiento;
                $sucursalProducto->save();
            } else {
                /*Existe sucursal_producto, deberíamos incrementar*/
                SucursalProducto::where([
                    "id_producto"=>$productoDetalle["id_producto"],
                    "id_sucursal"=>$data["id_sucursal"],
                    "precio_entrada"=>$productoDetalle["precio_unitario"],
                    "fecha_vencimiento"=>$fechaVencimiento,
                    "lote"=>$lote
                ])->increment('stock', $productoDetalle["cantidad"]);
            }

            $sucursalProductoHistorial = new SucursalProductoHistorial;
            $sucursalProductoHistorial->id_producto = $productoDetalle["id_producto"];
            $sucursalProductoHistorial->id_sucursal_destino = $data["id_sucursal"];
            $sucursalProductoHistorial->precio_entrada = $productoDetalle["precio_unitario"];
            $sucursalProductoHistorial->cantidad = $productoDetalle["cantidad"];
            $sucursalProductoHistorial->id_compra = $compra->id;
            $sucursalProductoHistorial->fecha_movimiento = $compra["fecha_compra"];
            $sucursalProductoHistorial->tipo_movimiento = 'E';
            $sucursalProductoHistorial->fecha_vencimiento = $fechaVencimiento;
            $sucursalProductoHistorial->lote = $lote;
            $sucursalProductoHistorial->save();

            $compraDetalle = new CompraDetalle;
            $compraDetalle->id_compra = $compra->id;
            $compraDetalle->item = $item;
            $compraDetalle->id_producto = $productoDetalle["id_producto"];
            $compraDetalle->cantidad = $productoDetalle["cantidad"];
            $compraDetalle->precio_unitario = $productoDetalle["precio_unitario"];
            $compraDetalle->fecha_vencimiento = $fechaVencimiento;
            $compraDetalle->lote = $lote;

            $compraDetalle->save();
        }

        return $compra;
    }

    public function anular(Compra $compra){
        /*
            ELIMINAR
        */
        $compra->load("detalle");

        foreach ($compra->detalle as $key => $detalle) {
            SucursalProducto::where([
                    "id_producto"=>$detalle->id_producto,
                    "precio_entrada"=>$detalle->precio_unitario,
                    "id_sucursal"=>$compra->id_sucursal,
                    "fecha_vencimiento"=>$detalle->fecha_vencimiento,
                    "lote"=>$detalle->lote
                ])
                ->decrement("stock", $detalle->cantidad);
        }

        SucursalProductoHistorial::where(["id_compra"=>$compra->id])->delete();
        $compra->delete();

        return $compra;
    }

}
