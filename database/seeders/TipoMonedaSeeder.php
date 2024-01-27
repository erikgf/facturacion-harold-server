<?php

namespace Database\Seeders;

use App\Models\TipoMoneda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoMonedaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoMoneda::truncate();

        $registros = [
            ['nombre'=>'SOLES', "id"=> "PEN", "abrev"=>"S/"],
        ];

        foreach ($registros as $key => $reg) {
            TipoMoneda::create($reg);
        }
    }
}
