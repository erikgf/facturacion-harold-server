<?php

namespace Database\Seeders;

use App\Models\CategoriaProducto;
use App\Models\TipoCategoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaProductoSeeder extends Seeder
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
            CategoriaProducto::truncate();
        }

        $csvFile = fopen(base_path("./database/data/categorias_v$version.csv"), "r");

        $columnas = ["nombre", "descripcion", "tipo_categoria"];
        $lineaActual = 0;
        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $colValues = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $colValues[$value] = utf8_encode($data[$key]);
                }

                $tipoCategoria = TipoCategoria::where(["nombre"=>$colValues["tipo_categoria"]])->first();

                $itemToCreate = [
                    "nombre"=>$colValues["nombre"],
                    "descripcion"=>$colValues["descripcion"],
                    "id_tipo_categoria"=>$tipoCategoria?->id,
                ];

                $item = CategoriaProducto::create($itemToCreate);
                $this->command->info('OK '.json_encode($item));
            }

            $lineaActual++;
        }

        fclose($csvFile);
    }
}


/*
SELECT ct.nombre, ct.descripcion, t.nombre as tipo FROM `categoria_producto` ct INNER JOIN tipo_categoria as t ON t.cod_tipo_categoria = ct.cod_tipo_categoria where ct.estado_mrcb;
*/
