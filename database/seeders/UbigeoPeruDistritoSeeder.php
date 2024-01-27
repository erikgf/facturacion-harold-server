<?php

namespace Database\Seeders;

use App\Models\UbigeoPeruDistrito;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UbigeoPeruDistritoSeeder extends Seeder
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
            UbigeoPeruDistrito::truncate();
        }

        $csvFile = fopen(base_path("./database/data/ubigeo_peru_districts_v$version.csv"), "r");

        $columnas = ["id", "nombre", "province_id", "department_id"];
        $lineaActual = 0;
        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $itemToCreate = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $itemToCreate[$value] = utf8_encode($data[$key]);
                }

                $item = UbigeoPeruDistrito::create($itemToCreate);
                $this->command->info('OK '.json_encode($item));
            }

            $lineaActual++;
        }

        fclose($csvFile);
    }
}
