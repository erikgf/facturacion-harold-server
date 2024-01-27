<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoDocumento::truncate();

        $registros = [
            ['id'=>'0','nombre'=>'DOC.TRIB.NO.DOM.SN.RUC','abrev'=>'S/D'],
            ['id'=>'1','nombre'=>'DOC. NACIONAL DE IDENTIDAD','abrev'=>'DNI'],
            ['id'=>'4','nombre'=>'CARNET DE EXTRANJERIA','abrev'=>'NULL'],
            ['id'=>'6','nombre'=>'REG. UNICO CONTRIBUYENTES','abrev'=>'RUC'],
            ['id'=>'7','nombre'=>'PASAPORTE','abrev'=>'NULL'],
            ['id'=>'A','nombre'=>'CED. DIPLOMATICA DE IDENTIDAD','abrev'=>'NULL'],

        ];

        foreach ($registros as $key => $reg) {
            TipoDocumento::create($reg);
        }
    }
}
