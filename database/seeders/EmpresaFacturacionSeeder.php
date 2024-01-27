<?php

namespace Database\Seeders;

use App\Models\EmpresaFacturacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaFacturacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $empresa = new EmpresaFacturacion;
        $empresa->nro_documento_empresa = "20603860871";
        $empresa->tipo_documento_empresa = "6";
        $empresa->nombre_comercial_empresa = "ANDREITA BABY KIDS";
        $empresa->razon_social_empresa = "Andredita Baby Kids S.A.";
        $empresa->razon_social_impresiones_empresa = "ANDREITA BABY KIDS S.A.";

        $empresa->codigo_ubigeo_empresa = "140101";
        $empresa->direccion_empresa = "Av. José Balta 1412, Int:103 Ivanlika - Chiclayo";
        $empresa->departamento_empresa = "LAMBAYEQUE";
        $empresa->provincia_empresa = "CHICLAYO";
        $empresa->distrito_empresa = "CHICLAYO";
        $empresa->telefono_empresa = "Telf. 074-503180";
        $empresa->resolucion_emision_empresa = "N° 097-2012/SUNAT";
        $empresa->codigo_pais_empresa = "PE";
        $empresa->razon_social_cotizacion_empresa = "ANDREITA BABY KIDS S.A.";
        $empresa->direccion_cotizacion_empresa = "Av. José Balta 1412, Int:103 Ivanlika - Chiclayo";
        $empresa->telefono_cotizacion_empresa = "Telf. 074-503180";
        $empresa->emisor_ruc = "20603860871";
        $empresa->emisor_usuario_sol = "SYST0001";
        $empresa->emisor_pass_sol = "Anaqueles1";
        $empresa->modo_proceso_emision = "3";

        $empresa->save();
    }
}
