<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::truncate();

        $registros = [
            ['id_tipo_documento'=>'1','numero_documento'=>'47291505','nombres'=>'HAROLD ORLANDO ','apellidos'=>'RUIZ JIMENEZ','direccion'=>'CALLE JUPITER 190 URB SANTA ELENA - CHICLAYO','correo'=>'','sexo'=>'M','celular'=>''],
            ['id_tipo_documento'=>'0','numero_documento'=>'','nombres'=>'VARIOS','apellidos'=>'VARIOS','direccion'=>'','correo'=>'','sexo'=>'M','celular'=>''],
            ['id_tipo_documento'=>'1','numero_documento'=>'41202847','nombres'=>'ROBINSON','apellidos'=>'BARRIO DE MENDOZA VASQUEZ','direccion'=>'LOS QUIPUS 1525 LA VICTORIA CHICLAYO','correo'=>'','sexo'=>'M','celular'=>'979903979'],
            ['id_tipo_documento'=>'1','numero_documento'=>'19240955','nombres'=>'BRENILDA','apellidos'=>'PEREZ TALAVERA','direccion'=>'Las manolias 203 Urb. Miraflores-Guadalupe-La Libertad','correo'=>'','sexo'=>'F','celular'=>'949894828'],
            ['id_tipo_documento'=>'1','numero_documento'=>'47080605','nombres'=>'MARGARITA','apellidos'=>' REGALADO RIOS','direccion'=>'REQUE CHICLAYO','correo'=>'','sexo'=>'F','celular'=>''],
        ];

        foreach ($registros as $key => $reg) {
            Cliente::create($reg);
        }
    }
}
