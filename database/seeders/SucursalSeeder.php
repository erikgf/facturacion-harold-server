<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Sucursal::truncate();

        $registros = [
            ['nombre'=>'CHICLAYO', "direccion"=> "AV. BALTA 1412 , INTERIOR 103 - GALERIA IVANLIKA - CHICLAYO", "telefono"=>"074503180"],
        ];

        foreach ($registros as $key => $reg) {
            Sucursal::create($reg);
        }
    }
}
