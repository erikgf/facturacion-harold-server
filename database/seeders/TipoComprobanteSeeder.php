<?php

namespace Database\Seeders;

use App\Models\TipoComprobante;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoComprobanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoComprobante::truncate();

        $registros = [
            ['id'=>'00','nombre'=>'TICKET','abrev'=>'T'],
            ['id'=>'01','nombre'=>'FACTURA','abrev'=>'F'],
            ['id'=>'03','nombre'=>'BOLETA DE VENTA','abrev'=>'B'],
            ['id'=>'07','nombre'=>'NOTA DE CRÉDITO','abrev'=>'NULL'],
            ['id'=>'08','nombre'=>'NOTA DE DÉBITO','abrev'=>'NULL'],
            ['id'=>'CO','nombre'=>'COTIZACION','abrev'=>'C'],
        ];

        foreach ($registros as $key => $reg) {
            TipoComprobante::create($reg);
        }
    }
}
