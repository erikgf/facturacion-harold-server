<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();

        $registros = [
            [
                'numero_documento'=>'16672096', "nombres_apellidos"=> "VICTOR RUIZ CARDOZO", "celular"=>"985249422",
                "fecha_nacimiento"=>"1968-04-15", "fecha_ingreso"=>"2024-01-09",
                "id_rol"=>1,"email"=>"harold.rj92@gmail.com", "acceso_sistema"=>true,
                "estado_activo"=>'A', "password"=>Hash::make('16672096')
            ],
        ];


        foreach ($registros as $key => $reg) {
            User::create($reg);
        }
    }
}
