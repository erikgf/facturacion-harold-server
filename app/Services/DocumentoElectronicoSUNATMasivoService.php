<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DocumentoElectronicoSUNATMasivoService {

    public function procesar(string $fechaEmision, string $tipoComprobante){ //01,03,07,08
            // Convert to Carbon instances for easier manipulation
        $comprobantes = (new DocumentoElectronicoService())->obtenerComprobantesEnviarPorFecha($fechaEmision, $tipoComprobante);
        $sunatService = new DocumentoElectronicoSUNATService();

        $res = [];
        if ($comprobantes->count() > 0){
            foreach ($comprobantes as $comprobante) {
                DB::beginTransaction();
                $id = (string) $comprobante->id;
                $resEnviar = $sunatService->enviarComprobante($id);

                $res[] = $resEnviar;
                DB::commit();
            }
        }

        return $res;
    }
}
