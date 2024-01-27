<?php

namespace Database\Seeders;

use App\Models\CategoriaProducto;
use App\Models\Marca;
use App\Models\Producto;
use App\Services\ProductoService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
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
            Producto::truncate();
        }

        $csvFile = fopen(base_path("./database/data/producto_v$version.csv"), "r");

        $columnas = ["nombre", "descripcion", "precio_unitario", "marca","categoria_producto"];
        $lineaActual = 0;
        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $colValues = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $colValues[$value] = utf8_encode($data[$key]);
                }

                $marca = Marca::where(["nombre"=>$colValues["marca"]])->first();
                $categoria = CategoriaProducto::where(["nombre"=>$colValues["categoria_producto"]])->first();

                $itemToCreate = [
                    "empresa_especial"=>"ANK",
                    "tallas"=>"",
                    "nombre"=>$colValues["nombre"],
                    "descripcion"=>$colValues["descripcion"],
                    "precio_unitario"=>$colValues["precio_unitario"],
                    "id_unidad_medida"=>"NIU",
                    "id_presentacion"=> 1,
                    "id_marca"=>$marca?->id,
                    "id_categoria_producto"=>$categoria?->id,
                    "numero_imagen_principal"=>1
                ];

                $item = (new ProductoService)->registrar($itemToCreate);
                $this->command->info('OK '.json_encode($item));
            }

            $lineaActual++;
        }

        fclose($csvFile);
    }
}


/*
SELECT p.nombre, p.descripcion, p.precio_unitario, m.nombre as marca, cp.nombre as categoria
FROM producto p
INNER JOIN marca m ON m.cod_marca = p.cod_marca
INNER JOIN categoria_producto cp ON cp.cod_categoria_producto = p.cod_producto where p.estado_mrcb;
*/
