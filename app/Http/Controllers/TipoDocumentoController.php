<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        return TipoDocumento::orderBy("nombre")->get(["id", "nombre"]);
    }
}
