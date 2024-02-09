<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProveedorRequest;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Proveedor::with([
                        "tipoDocumento" => function($query){
                            return $query->select("id", "nombre","abrev");
                        }
                    ])->get(["id", "id_tipo_documento", "numero_documento", "razon_social", "direccion", "correo","nombre_contacto","celular_contacto"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProveedorRequest $request)
    {
        $data = $request->validated();

        $proveedor = new Proveedor;

        $proveedor->id_tipo_documento = $data["id_tipo_documento"];
        $proveedor->numero_documento = $data["numero_documento"];
        $proveedor->razon_social = $data["razon_social"];
        $proveedor->direccion = $data["direccion"];
        $proveedor->correo = $data["correo"];
        $proveedor->nombre_contacto = $data["nombre_contacto"];
        $proveedor->celular_contacto = $data["celular_contacto"];

        $proveedor->save();

        return $proveedor;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Proveedor::with([
                    "tipoDocumento" => function($query){
                        return $query->select("id", "nombre","abrev");
                    }
                ])->findOrFail($id, ["id_tipo_documento", "numero_documento", "razon_social", "direccion", "correo", "nombre_contacto","celular_contacto"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProveedorRequest $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $data = $request->validated();
        $proveedor->update($data);

        return $proveedor;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $proveedor->delete();
        return $proveedor;
    }
}
