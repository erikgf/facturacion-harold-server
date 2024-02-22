<?php

namespace App\Http\Controllers;

use App\Models\TipoComprobante;

class TipoComprobanteController extends Controller
{
    public function index()
    {
        return TipoComprobante::orderBy("id")->get(["id", "nombre"]);
    }
}
