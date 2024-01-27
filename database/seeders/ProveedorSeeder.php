<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
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
            Proveedor::truncate();
        }

        $csvFile = fopen(base_path("./database/data/proveedor_v$version.csv"), "r");

        $columnas = ["id_tipo_documento", "numero_documento", "razon_social", "direccion","correo"];
        $lineaActual = 0;
        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $itemToCreate = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $itemToCreate[$value] = utf8_encode($data[$key]);
                }

                $item = Proveedor::create($itemToCreate);
                $this->command->info('OK '.json_encode($item));
            }

            $lineaActual++;
        }

        fclose($csvFile);

        $itemToCreate = [
            "id_tipo_documento"=>"6",
            "numero_documento"=>"20555555544",
            "razon_social"=>"CHALICEN SRL",
            "direccion"=>"LIMA2",
            "correo"=>"KK@K"
        ];

        $item = Proveedor::create($itemToCreate);
        $this->command->info('OK '.json_encode($item));
    }
}
