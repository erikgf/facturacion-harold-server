<?php

namespace App\Http\Controllers;

use App\Services\VentaReportesService;
use Illuminate\Http\Request;

class VentaReportesController extends Controller
{
    public function obtenerGeneral(Request $request){
        $data = $request->validate([
            "fecha_desde"=>"required|date",
            "fecha_hasta"=>"required|date",
            "todos"=>"required|integer",
            "sucursal"=>"nullable|string",
            "cliente"=>"nullable|string"
        ]);

        $fechaDesde = $data["fecha_desde"];
        $fechaHasta = $data["fecha_hasta"];
        $todos = (bool) $data["todos"];
        $sucursal =  @$data['sucursal']?: "";
        $cliente =  @$data['cliente']?: "";

        return  (new VentaReportesService)->general($fechaDesde, $fechaHasta, $todos, $sucursal, $cliente);
    }

    public function obtenerMasVendido(Request $request){
        $data = $request->validate([
            "fecha_desde"=>"required|date",
            "fecha_hasta"=>"required|date",
            "todos"=>"required|integer",
            "sucursal"=>"nullable|string",
        ]);

        $fechaDesde = $data["fecha_desde"];
        $fechaHasta = $data["fecha_hasta"];
        $todos = (bool) $data["todos"];
        $sucursal =  @$data['sucursal']?: "";

        return  (new VentaReportesService)->masVendido($fechaDesde, $fechaHasta, $todos, $sucursal);
    }

}
