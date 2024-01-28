<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentoElectronicoResumenDiarioRequest;
use App\Services\DocumentoElectronicoResumenDiarioService;
use App\Services\DocumentoElectronicoResumenDiarioSUNATService;
use App\Services\DocumentoElectronicoResumenDiarioXMLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentoElectronicoResumenDiarioController extends Controller
{
    public function index(Request $request){
        $data = $request->validate([
            "fecha_inicio"=>"required|date",
            "fecha_fin"=>"required|date"
        ]);

        $fechaFin = $data["fecha_fin"];
        $fechaInicio = $data["fecha_inicio"];

        return (new DocumentoElectronicoResumenDiarioService)->listar($fechaInicio, $fechaFin);
    }

    public function store(DocumentoElectronicoResumenDiarioRequest $request){
        $data = $request->validated();
        DB::beginTransaction();
        $rd = (new DocumentoElectronicoResumenDiarioService)->registrar($data, true, true);
        DB::commit();
        return $rd;
    }

    public function show(string $id){
        return (new DocumentoElectronicoResumenDiarioService)->leer($id);
    }

    public function destroy(string $id){
        DB::beginTransaction();
        $rd = (new DocumentoElectronicoResumenDiarioService)->anular($id);;
        DB::commit();
        return $rd;
    }

    public function obtenerComprobantesPorFecha(string $fecha){
        return (new DocumentoElectronicoResumenDiarioService)->obtenerComprobantesPorFecha($fecha);
    }

    public function generarXML(string $id){
        DB::beginTransaction();
        $rd = (new DocumentoElectronicoResumenDiarioXMLService)->generarComprobanteXML($id);
        DB::commit();
        return $rd;
    }

    public function enviarSUNAT(string $id){
        DB::beginTransaction();
        $rd = (new DocumentoElectronicoResumenDiarioSUNATService)->enviarComprobante($id);
        DB::commit();
        return $rd;
    }

    public function consultarTicket(string $id){
        DB::beginTransaction();
        $rd = (new DocumentoElectronicoResumenDiarioSUNATService)->consultarTicket($id);
        DB::commit();
        return $rd;
    }

}
