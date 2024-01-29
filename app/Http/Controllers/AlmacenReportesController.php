<?php

namespace App\Http\Controllers;

use App\Services\AlmacenReportesService;
use Illuminate\Http\Request;

class AlmacenReportesController extends Controller
{
    public function obtenerStock(Request $request){
        $data = $request->validate([
            "sucursal"=>"nullable|string",
        ]);

        $sucursal =  @$data['sucursal']?: "";

        return  (new AlmacenReportesService)->stock($sucursal);
    }

    public function obtenerKardex(Request $request){
        $data = $request->validate([
            "sucursal"=>"nullable|string",
            "producto"=>"nullable|string",
        ]);

        $producto = @$data["producto"]?: "";
        $sucursal =  @$data['sucursal']?: "";

        return  (new AlmacenReportesService)->kardex($sucursal, $producto);
    }
}
