<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        return Rol::get(["id", "nombre"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "nombre"=> "required|string",
        ]);

        $rol = new Rol;
        $rol->nombre = $data["nombre"];
        $rol->save();

        return $rol;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Rol::findOrFail($id, ["id", "nombre"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rol = Rol::findOrFail($id);

        $data = $request->validate([
            "nombre"=> "required|string",
        ]);

        $rol->update($data);

        return $rol;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rol = Rol::findOrFail($id);

        $rol->delete();
        return $rol;
    }
}
