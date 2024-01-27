<?php

namespace Database\Seeders;

use App\Models\MotivoNotaSunat;
use Illuminate\Database\Seeder;

class MotivoNotaSunatSeeder extends Seeder
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
            MotivoNotaSunat::truncate();
        }

        $csvFile = fopen(base_path("./database/data/motivo_nota_v$version.csv"), "r");

        $columnas = ["id_tipo_motivo", "id_tipo_nota", "descripcion"];
        $lineaActual = 0;
        while (($data = fgetcsv($csvFile, 2000, $this->separador)) !== FALSE) {
            if ($lineaActual > $this->lineaInicio){
                $itemToCreate = [];
                foreach ($columnas as $key => $value) {
                    if (!$value) continue;
                    $itemToCreate[$value] = utf8_encode($data[$key]);
                }

                $item = MotivoNotaSunat::create($itemToCreate);
                $this->command->info('OK '.json_encode($item));
            }

            $lineaActual++;
        }

        fclose($csvFile);
    }
}
