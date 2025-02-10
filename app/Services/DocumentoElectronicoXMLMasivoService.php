<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DocumentoElectronicoXMLMasivoService {

    public function procesar(string $fechaEmision, string $tipoComprobante){ //01,03,07,08
            // Convert to Carbon instances for easier manipulation
        $comprobantes = (new DocumentoElectronicoService())->obtenerComprobantesGenerarPorFecha($fechaEmision, $tipoComprobante);
        $xmlService = new DocumentoElectronicoXMLService();

        $res = [];
        if ($comprobantes->count() > 0){
            foreach ($comprobantes as $comprobante) {
                DB::beginTransaction();
                $id = (string) $comprobante->id;
                $resGenerar = $xmlService->generarComprobanteXML($id);
                $resFirmar = $xmlService->firmarComprobanteXML($id);

                $res[] = [
                    "generar"=>$resGenerar,
                    "firmar"=>$resFirmar
                ];
                DB::commit();
            }
        }

        return $res;
    }
}
