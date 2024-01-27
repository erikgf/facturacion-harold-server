<?php

namespace App\Http\Controllers;

use App\Models\TipoComprobante;
use Illuminate\Http\Request;

class TipoComprobanteController extends Controller
{
    public function index()
    {
        return TipoComprobante::orderBy("id")->get(["id", "nombre"]);
    }
}
