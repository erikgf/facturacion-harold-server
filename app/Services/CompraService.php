<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\SucursalProducto;
use App\Models\SucursalProductoHistorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompraService {

    public function registrar(array $data){
        $compra = new Compra();
        return $this->guardar($compra, $data);
    }

    public function editar(array $data, int $id){
        $compra = Compra::findOrFail($id);
        $this->revertirCompraAlmacen($compra);
        return $this->guardar($compra, $data);
    }

    private function guardar(Compra $compra, $data) {
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
                throw ValidationException::withMessages(["Producto {$item} no existe en el sistema"]);
            }

            if ($productoDetalle["cantidad"] <= 0){
                throw ValidationException::withMessages(["Producto de la fila ".($item)." no tiene cantidad válida."]);
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

    public function leer(int $id){
        $compra = Compra::with([
                    "sucursal"=>fn($q) =>  $q->select("id", "nombre"),
                    "proveedor",
                    "detalle"=>function($q){
                        $q->leftJoin("sucursal_productos as sp", function($join){
                            $join->on("sp.id_producto","=","compra_detalles.id");
                            $join->on("sp.fecha_vencimiento", "=", "compra_detalles.fecha_vencimiento");
                            $join->on("sp.lote", "=", "compra_detalles.lote");
                        });
                        $q->select("compra_detalles.id", "id_compra", "compra_detalles.id_producto","item", "cantidad",
                                        "compra_detalles.fecha_vencimiento", "compra_detalles.lote", "precio_unitario",
                                        "sp.stock",
                                        DB::raw("(compra_detalles.cantidad * compra_detalles.precio_unitario) as subtotal"));
                        $q->with([
                            "producto"=>function($q){
                                $q->select("id", "nombre","id_marca");
                                $q->with([
                                    "marca"
                                ]);
                            }]);
                    }])
                    ->select(
                            "id",
                            "id_tipo_comprobante", "numero_comprobante", "id_sucursal",
                            "id_proveedor", "tipo_pago", "tipo_tarjeta",
                            "importe_total", "fecha_compra", "hora_compra",
                            "guias_remision", "observaciones"
                            )
                    ->findOrFail($id);

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
        $compra->detalle()->delete();
        $compra->delete();

        return $compra;
    }

    private function revertirCompraAlmacen(Compra $compra){
        /*
            1.- estos registros deben ser RETIRADOS del movimiento asociado a esta compra.
            2.- se debe limpiar el detalle de la compra
            3.- con compra se editará
            4.- creará de nuevo el detalle y los comovimientos
        */
        $id_sucursal = $compra->id_sucursal;
        $sucursalProductoHistorial = SucursalProductoHistorial::query()
                                        ->where(["id_compra"=>$compra->id])
                                        ->select("id","id_producto", "fecha_vencimiento", "lote", "cantidad")
                                        ->get();

        foreach ($sucursalProductoHistorial as $producto) {
            SucursalProducto::where([
                "id_producto" => $producto->id_producto,
                "id_sucursal" => $id_sucursal,
                "fecha_vencimiento"=>$producto->fecha_vencimiento,
                "lote"=>$producto->lote,
                "precio_entrada"=>$producto->precio_entrada
            ])->decrement("stock", $producto->cantidad);
        }

        SucursalProductoHistorial::query()
                                    ->where(["id_compra"=>$compra->id])
                                    ->forceDelete();

        CompraDetalle::query()
                        ->where(["id_compra"=>$compra->id])
                        ->forceDelete();
    }

}
