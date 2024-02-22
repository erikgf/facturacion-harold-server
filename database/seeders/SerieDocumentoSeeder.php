<?php

namespace Database\Seeders;

use App\Models\SerieDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SerieDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SerieDocumento::truncate();

        $registros = [
            ['id_tipo_comprobante'=>'01','serie'=>'F002','correlativo'=>1],
            ['id_tipo_comprobante'=>'03','serie'=>'B002','correlativo'=>1],
            ['id_tipo_comprobante'=>'07','serie'=>'B002','correlativo'=>1],
            ['id_tipo_comprobante'=>'07','serie'=>'F002','correlativo'=>1],
            ['id_tipo_comprobante'=>'08','serie'=>'B002','correlativo'=>1],
            ['id_tipo_comprobante'=>'08','serie'=>'F002','correlativo'=>1]
        ];

        foreach ($registros as $key => $reg) {
            SerieDocumento::create($reg);
        }
    }
}
