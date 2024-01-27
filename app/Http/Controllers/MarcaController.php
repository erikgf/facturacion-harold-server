<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Marca::get(["id", "nombre"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "nombre"=> "required|string",
        ]);

        $marca = new Marca;
        $marca->nombre = $data["nombre"];
        $marca->save();

        return $marca;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Marca::findOrFail($id, ["id", "nombre"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $marca = Marca::findOrFail($id);

        $data = $request->validate([
            "nombre"=> "required|string",
        ]);

        $marca->update($data);

        return $marca;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $marca = Marca::findOrFail($id);

        $marca->delete();
        return $marca;
    }
}
