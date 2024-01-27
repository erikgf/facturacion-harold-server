<?php

namespace App\Http\Controllers;

use App\Http\Requests\SucursalRequest;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(){
        return Sucursal::orderBy("nombre")->get(["id", "nombre","direccion","telefono"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SucursalRequest $request)
    {
        $data = $request->validated();

        $marca = new Sucursal;
        $marca->nombre = $data["nombre"];
        $marca->direccion = $data["direccion"];
        $marca->telefono = $data["telefono"];
        $marca->save();

        return $marca;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Sucursal::findOrFail($id, ["id", "nombre","direccion","telefono"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SucursalRequest $request, string $id)
    {
        $marca = Sucursal::findOrFail($id);

        $data = $request->validated();
        $marca->update($data);

        return $marca;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $marca = Sucursal::findOrFail($id);

        $marca->delete();
        return $marca;
    }
}
