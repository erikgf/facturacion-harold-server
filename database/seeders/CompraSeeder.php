<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\CompraService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompraSeeder extends Seeder
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
            Compra::truncate();
            CompraDetalle::truncate();
        }

        $csvFile = fopen(base_path("./database/data/compra_v$version.csv"), "r");

        $columnas = ["id_compra","numero_documento_proveedor", "numero_comprobante", "importe_total", "id_tipo_comprobante","fecha_compra","observaciones", "guias_remision",
                        "item", "producto", "precio_unitario", "cantidad", "fecha_vencimiento", "lote"];
        $lineaActual = 0;
        $ultimoIdCompra = NULL;
        $compra = NULL;

        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $colValues = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $colValues[$value] = utf8_encode($data[$key]);
                }

                if ($ultimoIdCompra != $colValues["id_compra"]){
                    if ($compra){
                        DB::beginTransaction();
                        $item = (new CompraService)->registrar($compra);
                        DB::commit();
                        $this->command->info('OK '.json_encode($compra)."\n");
                        $compra = NULL;
                    }

                    if ($compra == NULL){
                        $objProveedor = Proveedor::where(["numero_documento"=>$colValues["numero_documento_proveedor"]])->first();
                        $compra = [
                            "numero_comprobante"=>$colValues["numero_comprobante"],
                            "id_tipo_comprobante"=>$colValues["id_tipo_comprobante"],
                            "id_proveedor"=>$objProveedor->id,
                            "tipo_pago"=>"E",
                            "tipo_tarjeta"=>NULL,
                            "observaciones"=>$colValues["observaciones"],
                            "guias_remision"=>$colValues["guias_remision"],
                            "fecha_compra"=>$colValues["fecha_compra"],
                            "hora_compra"=>"16:00",
                            "importe_total"=>$colValues["importe_total"],
                            "id_sucursal"=>1,
                            "id_usuario_registro"=>1,
                            "productos"=>[]
                        ];
                    }
                }

                $objProducto = Producto::where(["nombre"=>$colValues["producto"]])->first();
                array_push($compra["productos"],
                    [
                        "id_producto"=>$objProducto->id,
                        "cantidad"=>$colValues["cantidad"],
                        "precio_unitario"=>$colValues["precio_unitario"],
                        "lote"=>$colValues["lote"],
                    ]
                );

                $ultimoIdCompra = $colValues["id_compra"];
            }
            $lineaActual++;
        }

        if ($compra){
            DB::beginTransaction();
            $item = (new CompraService)->registrar($compra);
            DB::commit();
            $this->command->info('OK '.json_encode($compra)."\n");
        }

        fclose($csvFile);
    }
}


/*
SELECT c.cod_compra, p.numero_documento, c.numero_comprobante, c.importe_total_compra, t.cod_tipo_comprobante, t.fecha_transaccion, t.observaciones, t.guias_remision,
cd.item, pr.nombre, cd.precio_unitario, cd.cantidad, cd.fecha_vencimiento, cd.lote
FROM `compra` c
INNER JOIN transaccion t ON c.cod_transaccion = t.cod_transaccion
INNER JOIN proveedor p ON p.cod_proveedor = c.cod_proveedor
INNER JOIN compra_detalle cd ON cd.cod_compra = c.cod_compra
INNER JOIN producto pr ON pr.cod_producto = cd.cod_producto
WHERE t.estado = 1;
*/
