<?php

namespace Database\Seeders;

use App\Models\PermisoRol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermisoRolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        PermisoRol::truncate();

        $registros = [
            ['id_permiso'=>'1','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'2','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'2','id_rol'=>'2','estado'=>'A'],
            ['id_permiso'=>'3','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'4','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'5','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'7','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'8','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'9','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'10','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'11','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'12','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'13','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'13','id_rol'=>'2','estado'=>'A'],
            ['id_permiso'=>'14','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'15','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'17','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'18','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'19','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'20','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'21','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'21','id_rol'=>'2','estado'=>'A'],
            ['id_permiso'=>'22','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'23','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'24','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'25','id_rol'=>'1','estado'=>'A'],
            //['id_permiso'=>'26','id_rol'=>'1','estado'=>'A'],
            ['id_permiso'=>'27','id_rol'=>'1','estado'=>'A'],
            //['id_permiso'=>'28','id_rol'=>'1','estado'=>'A']
        ];

        foreach ($registros as $key => $reg) {
            PermisoRol::create($reg);
        }


    }
}
