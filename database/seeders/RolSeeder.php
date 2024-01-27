<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::truncate();

        $registros = [
            ['nombre'=>'ADMINISTRADOR'],
            ['nombre'=>'VENDEDOR'],
        ];

        foreach ($registros as $key => $reg) {
            Rol::create($reg);
        }
    }
}
