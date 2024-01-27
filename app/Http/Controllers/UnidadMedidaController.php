<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        return UnidadMedida::get(["id", "codigo_sunat","descripcion"]);
    }
}
