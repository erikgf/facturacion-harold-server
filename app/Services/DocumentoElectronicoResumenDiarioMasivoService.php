<?php

namespace App\Services;

use App\Models\DocumentoElectronicoResumenDiario;
use App\Models\EmpresaFacturacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentoElectronicoResumenDiarioMasivoService {

    public function procesar(string $fechaDesde, string $fechaHasta, string $status){

            // Convert to Carbon instances for easier manipulation
        $start = Carbon::parse($fechaDesde);
        $end = Carbon::parse($fechaHasta);

        // Ensure the start date is not after the end date
        if ($start->gt($end)) {
            return [];
        }

        $dates = [];
        while ($start->lte($end)) {
            $dates[] = $start->toDateString(); // Add the date in 'Y-m-d' format
            $start->addDay(); // Increment by one day
        }

        $fecha_generacion = date('Y-m-d');
        $serie = Str::replace('-', '', $fecha_generacion);
        $ruc_empresa = EmpresaFacturacion::findOrFail(1, ["nro_documento_empresa"])->nro_documento_empresa;
        $secuencia = DocumentoElectronicoResumenDiario::where(["serie"=>$serie])->max("secuencia") + 1;

        $rdService = new DocumentoElectronicoResumenDiarioService();

        $arregloFechasProcesadasCorrectamente = [];
        foreach ($dates as $fecha_emision) {
            $comprobantes = $rdService->obtenerComprobantesPorFecha($fecha_emision);
            if ($comprobantes->count() > 0){
                DB::beginTransaction();
                $res = $rdService->registrarParaMasivo(
                    $fecha_generacion,
                    $serie,
                    $ruc_empresa,
                    $secuencia,
                    $fecha_emision,
                    $status,
                    $comprobantes
                );

                DB::commit();

                if ($res){
                    $arregloFechasProcesadasCorrectamente[] = $fecha_emision;
                }
            }

            $secuencia++;
        }

        return $arregloFechasProcesadasCorrectamente;
    }

}
