<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Services\VentaService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaSeeder extends Seeder
{
    private $separador = ";";
    private $lineaInicio = 0;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->run_vN(1);
    }

    private function run_vN($version)
    {
        if ($version === 1){
            Venta::truncate();
            VentaDetalle::truncate();
        }

        $csvFile = fopen(base_path("./database/data/venta_v$version.csv"), "r");

        $columnas = ["id_venta","cod_tipo_comprobante", "serie", "correlativo", "cliente","observaciones", "fecha_venta", "importe_total",
                        "item", "producto",  "fecha_vencimiento", "lote",  "cantidad", "precio_unitario"];
        $lineaActual = 0;
        $ultimoIdVenta = NULL;
        $venta = NULL;

        $serieVentaSC = "T001";
        $correlativoVentasSC = 1;

        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $colValues = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $colValues[$value] = utf8_encode($data[$key]);
                }

                if ($ultimoIdVenta != $colValues["id_venta"]){
                    if ($venta){
                        DB::beginTransaction();
                        $item = (new VentaService)->registrar($venta);
                        DB::commit();
                        $this->command->info('OK '.json_encode($item)."\n");
                        $venta = NULL;
                    }

                    if ($venta == NULL){
                        $objCliente = Cliente::where(["numero_documento"=>$colValues["cliente"]])->first();
                        $venta = [
                            "id_tipo_comprobante"=>$colValues["cod_tipo_comprobante"],
                            "serie"=>$colValues["cod_tipo_comprobante"] == '00' ? $serieVentaSC : $colValues["serie"],
                            "correlativo"=>$colValues["cod_tipo_comprobante"] == '00' ? $correlativoVentasSC++ : $colValues["correlativo"],
                            "id_cliente"=>$objCliente->id,
                            "monto_efectivo"=>$colValues["importe_total"],
                            "monto_tarjeta"=>"0.00",
                            "monto_credito"=>"0.00",
                            "monto_yape"=>"0.00",
                            "monto_plin"=>"0.00",
                            "monto_transferencia"=>"0.00",
                            "descuento_global"=>"0.00",
                            "observaciones"=>$colValues["observaciones"] == "NULL" ? NULL : $colValues["observaciones"],
                            "fecha_venta"=>$colValues["fecha_venta"],
                            "hora_venta"=>"13:00",
                            "importe_total"=>$colValues["importe_total"],
                            "id_sucursal"=>1,
                            "id_usuario_registro"=>1,
                            "productos"=>[]
                        ];
                    }
                }

                $objProducto = Producto::where(["nombre"=>$colValues["producto"]])->first();
                array_push($venta["productos"],
                    [
                        "id_producto"=>$objProducto->id,
                        "cantidad"=>$colValues["cantidad"],
                        "precio_unitario"=>$colValues["precio_unitario"],
                        "fecha_vencimiento"=>$colValues["fecha_vencimiento"],
                        "lote"=>$colValues["lote"],
                    ]
                );

                $ultimoIdVenta = $colValues["id_venta"];
            }
            $lineaActual++;
        }

        if ($venta){
            DB::beginTransaction();
            $item = (new VentaService)->registrar($venta);
            DB::commit();
            $this->command->info('OK '.json_encode($venta)."\n");
        }

        fclose($csvFile);
    }
}


/*

SELECT v.cod_venta, (CASE t.cod_tipo_comprobante WHEN '' THEN '00' ELSE t.cod_tipo_comprobante END) as cod_tipo_comprobante,
CONCAT((CASE t.cod_tipo_comprobante WHEN '' THEN 'T' WHEN '03' THEN 'B' ELSE 'F' END), serie) as serie, t.correlativo,
 c.numero_documento as cliente, t.observaciones, t.fecha_transaccion as fecha_venta, v.importe_total_venta,
 vd.item, pr.nombre as producto, vd.fecha_vencimiento, vd.lote, vd.cantidad_item, vd.precio_venta_unitario FROM `venta` v
 INNER JOIN transaccion t ON t.cod_transaccion = v.cod_transaccion
 INNER JOIN cliente c ON c.cod_cliente = v.cod_cliente
 INNER JOIN venta_detalle vd ON vd.cod_venta = v.cod_venta
 INNER JOIN producto pr ON pr.cod_producto = vd.cod_producto WHERE t.estado = 1;

*/
