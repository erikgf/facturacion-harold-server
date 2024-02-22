<?php

namespace App\Http\Controllers;

use App\Models\SerieDocumento;
use Illuminate\Http\Request;

class SerieDocumentoController extends Controller
{
    public function index(){
        return SerieDocumento::with(["tipoComprobante"=>function($q){
            $q->select("id", "nombre");
        }])
        ->get([
            "id", "serie", "correlativo", "id_tipo_comprobante"
        ]);
    }

    public function store(Request $request){
        $data = $request->validate([
            "id_tipo_comprobante"=>"required|string|size:2",
            "serie"=> "required|string|size:4",
            "correlativo"=>"required|integer",
        ]);

        $serieDoc = new SerieDocumento();

        $serieDoc->id_tipo_comprobante = $data["id_tipo_comprobante"];
        $serieDoc->serie = $data["serie"];
        $serieDoc->correlativo = $data["correlativo"];

        $serieDoc->save();

        return $serieDoc;
    }

    public function show(string $id){
        return SerieDocumento::findOrFail($id, [
                "id", "serie", "correlativo", "id_tipo_comprobante"
            ]);
    }

    public function update(Request $request, string $id){
        $serieDoc = SerieDocumento::findOrFail($id);

        $data = $request->validate([
            "id_tipo_comprobante"=>"required|string|size:2",
            "serie"=> "required|string|size:4",
            "correlativo"=>"required|integer",
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
            "serie", "correlativo"
        ]);
    }
}
