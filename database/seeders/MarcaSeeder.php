<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Marca::truncate();

        $registros = [
            ['nombre'=>'HARDY'],
            ['nombre'=>'PAPOTITOS'],
            ['nombre'=>'KUKULI'],
            ['nombre'=>'YORYAN'],
            ['nombre'=>'YOSHIRO'],
            ['nombre'=>'MUQUIS'],
            ['nombre'=>'OSLO'],
            ['nombre'=>'MAYORAL'],
            ['nombre'=>'WOOPY'],
            ['nombre'=>'CHALICEN'],
            ['nombre'=>'BABY CLUB'],
            ['nombre'=>'KOATITOS'],
            ['nombre'=>'BABY MAX'],
            ['nombre'=>'BABY DU'],
            ['nombre'=>'OBANDO'],
            ['nombre'=>'DJ KIDS'],
            ['nombre'=>'WALKIDS'],
            ['nombre'=>'LUPER'],
            ['nombre'=>'CHIQUITUNS'],
            ['nombre'=>'CHIQUIPANDA'],
            ['nombre'=>'BURBUJITAS'],
            ['nombre'=>'CHOCOLINDO'],
            ['nombre'=>'PECOSITO'],
            ['nombre'=>'PECOSITA'],
            ['nombre'=>'MIGUELITO'],
            ['nombre'=>'CARMELL'],
            ['nombre'=>'KOULI'],
            ['nombre'=>'BABY JHENS'],
            ['nombre'=>'RISMAR'],
            ['nombre'=>'SONRISITAS'],
            ['nombre'=>'OMNIPOL'],
            ['nombre'=>'SHYIOL'],
            ['nombre'=>'MANIS'],
            ['nombre'=>'ANGELA'],
            ['nombre'=>'CHIQUILINDOS'],
            ['nombre'=>'CUNITA'],
            ['nombre'=>'PIBEBE'],
        ];

        foreach ($registros as $key => $reg) {
            Marca::create($reg);
        }
    }
}
