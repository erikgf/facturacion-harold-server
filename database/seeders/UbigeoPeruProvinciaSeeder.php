<?php

namespace Database\Seeders;

use App\Models\UbigeoPeruProvincia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UbigeoPeruProvinciaSeeder extends Seeder
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
            UbigeoPeruProvincia::truncate();
        }

        $csvFile = fopen(base_path("./database/data/ubigeo_peru_provinces_v$version.csv"), "r");

        $columnas = ["id", "nombre", "department_id"];
        $lineaActual = 0;
        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $itemToCreate = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $itemToCreate[$value] = utf8_encode($data[$key]);
                }

                $item = UbigeoPeruProvincia::create($itemToCreate);
                $this->command->info('OK '.json_encode($item));
            }

            $lineaActual++;
        }

        fclose($csvFile);
    }
}
