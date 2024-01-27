<?php

namespace Database\Seeders;

use App\Models\Presentacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresentacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Presentacion::truncate();

        $registros = [
            ['nombre'=>'- NO APLICA -'],
            ['nombre'=>'BOTELLA'],
            ['nombre'=>'GALON'],
            ['nombre'=>'BOLSA'],
            ['nombre'=>'CAJA'],
            ['nombre'=>'LENTE'],
            ['nombre'=>'TUBO'],
            ['nombre'=>'PAQUETE'],
            ['nombre'=>'ROLLO'],
            ['nombre'=>'SOBRE'],
        ];

        foreach ($registros as $key => $reg) {
            Presentacion::create($reg);
        }

    }
}
