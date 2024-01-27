<?php

namespace App\Http\Controllers;

use App\Models\TipoCategoria;
use Illuminate\Http\Request;

class TipoCategoriaController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TipoCategoria::orderBy("nombre")->get(["id", "nombre","descripcion"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "nombre"=> "required|string|max:200",
            "descripcion"=> "nullable|string",
        ]);

        $tipoCategoria = new TipoCategoria;
        $tipoCategoria->nombre = $data["nombre"];
        $tipoCategoria->descripcion = $data["descripcion"];
        $tipoCategoria->save();

        return $tipoCategoria;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return TipoCategoria::findOrFail($id, ["id", "nombre","descripcion"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipoCategoria = TipoCategoria::findOrFail($id);

        $data = $request->validate([
            "nombre"=> "required|string|max:200",
            "descripcion"=> "nullable|string",
        ]);

        $tipoCategoria->update($data);

        return $tipoCategoria;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipoCategoria = TipoCategoria::findOrFail($id);

        $tipoCategoria->delete();
        return $tipoCategoria;
    }
}
