<?php

namespace App\Http\Controllers;

use App\Models\SerieDocumento;
use Illuminate\Http\Request;

class SerieDocumentoController extends Controller
{
    public function index(){
        return SerieDocumento::with([
                "tipoComprobante"=>fn($q) => $q->select("id", "nombre"),
                "sucursal"=>fn($q) => $q->select("id", "nombre")
            ])
            ->get([
                "id", "serie", "correlativo", "id_tipo_comprobante", "id_sucursal"
            ]);
    }

    public function store(Request $request){
        $data = $request->validate([
            "id_tipo_comprobante"=>"required|string|size:2",
            "serie"=> "required|string|size:4",
            "correlativo"=>"required|integer",
            "id_sucursal"=>"required|integer|exists:sucursals,id,deleted_at,NULL"
        ]);

        $serieDoc = new SerieDocumento();

        $serieDoc->id_tipo_comprobante = $data["id_tipo_comprobante"];
        $serieDoc->serie = $data["serie"];
        $serieDoc->correlativo = $data["correlativo"];
        $serieDoc->id_sucursal = $data["id_sucursal"];

        $serieDoc->save();

        return $serieDoc;
    }

    public function show(string $id){
        return SerieDocumento::query()
                    ->findOrFail($id, [
                        "id", "serie", "correlativo", "id_tipo_comprobante","id_sucursal"
                    ]);
    }

    public function update(Request $request, string $id){
        $serieDoc = SerieDocumento::findOrFail($id);

        $data = $request->validate([
            "id_tipo_comprobante"=>"required|string|size:2",
            "serie"=> "required|string|size:4",
            "correlativo"=>"required|integer",
            "id_sucursal"=>"required|integer|exists:sucursals,id,deleted_at,NULL"
        ]);

        $serieDoc->update($data);

        return $serieDoc;
    }

    public function destroy(string $id){
        $serieDoc = SerieDocumento::findOrFail($id);
        $serieDoc->delete();
        return $serieDoc;
    }

    public function getSeriePorTipoComprobante(string $id){
        return SerieDocumento::where([
            "id_tipo_comprobante"=>$id
        ])->get([
            "serie", "correlativo", "id_sucursal"
        ]);
    }
}
