<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        UnidadMedida::truncate();

        $registros = [
            ['id'=>'KGM','codigo_sunat'=>'01','descripcion'=>'KILOGRAMOS'],
            ['id'=>'LBR','codigo_sunat'=>'02','descripcion'=>'LIBRAS'],
            ['id'=>'STN','codigo_sunat'=>'04','descripcion'=>'TONELADAS'],
            ['id'=>'GRM','codigo_sunat'=>'06','descripcion'=>'GRAMOS'],
            ['id'=>'NIU','codigo_sunat'=>'07','descripcion'=>'UNIDADES'],
            ['id'=>'LTR','codigo_sunat'=>'08','descripcion'=>'LITROS'],
            ['id'=>'GAL','codigo_sunat'=>'09','descripcion'=>'GALONES'],
            ['id'=>'BLL','codigo_sunat'=>'10','descripcion'=>'BARRILES'],
            ['id'=>'CA','codigo_sunat'=>'11','descripcion'=>'LATAS'],
            ['id'=>'BX','codigo_sunat'=>'12','descripcion'=>'CAJAS'],
            ['id'=>'MLD','codigo_sunat'=>'13','descripcion'=>'MILLARES'],
            ['id'=>'MTQ','codigo_sunat'=>'14','descripcion'=>'METROS CUBICOS'],
            ['id'=>'MTR','codigo_sunat'=>'15','descripcion'=>'METROS'],
            ['id'=>'PR','codigo_sunat'=>'PR','descripcion'=>'PAR'],
            ['id'=>'H87','codigo_sunat'=>'17','descripcion'=>'PIEZA'],
            ['id'=>'XRO','codigo_sunat'=>'18','descripcion'=>'ROLLOS'],
        ];

        foreach ($registros as $key => $reg) {
            UnidadMedida::create($reg);
        }
    }
}
