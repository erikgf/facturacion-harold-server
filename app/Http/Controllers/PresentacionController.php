<?php

namespace App\Http\Controllers;

use App\Models\Presentacion;
use Illuminate\Http\Request;

class PresentacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Presentacion::get(["id", "nombre"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "nombre"=> "required|string",
        ]);

        $presentacion = new Presentacion;
        $presentacion->nombre = $data["nombre"];
        $presentacion->save();

        return $presentacion;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Presentacion::findOrFail($id, ["id", "nombre"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $presentacion = Presentacion::findOrFail($id);

        $data = $request->validate([
            "nombre"=> "required|string",
        ]);

        $presentacion->update($data);

        return $presentacion;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $presentacion = Presentacion::findOrFail($id);

        $presentacion->delete();
        return $presentacion;
    }
}
