<?php

namespace App\Http\Controllers;

use App\Models\TipoMoneda;

class TipoMonedaController extends Controller
{
    public function index()
    {
        return TipoMoneda::get(["id", "nombre","abrev"]);
    }
}
