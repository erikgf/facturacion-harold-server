<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        return Cliente::with([
                        "tipoDocumento" => function($query){
                            return $query->select("id", "nombre","abrev");
                        }
                    ])->get(["id", "id_tipo_documento", "numero_documento", "nombres", "apellidos", "direccion", "correo", "sexo", "celular", "fecha_nacimiento", "numero_contacto"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClienteRequest $request)
    {
        $data = $request->validated();

        $cliente = new Cliente;

        $cliente->id_tipo_documento = $data["id_tipo_documento"];
        $cliente->numero_documento = $data["numero_documento"];
        $cliente->nombres = $data["nombres"];
        $cliente->apellidos = $data["apellidos"];
        $cliente->direccion = $data["direccion"];
        $cliente->correo = $data["correo"];
        $cliente->sexo = $data["sexo"];
        $cliente->celular = $data["celular"];
        $cliente->fecha_nacimiento = $data["fecha_nacimiento"];
        $cliente->numero_contacto = $data["numero_contacto"];

        $cliente->save();

        return $cliente;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Cliente::with([
                    "tipoDocumento" => function($query){
                        return $query->select("id", "nombre", "abrev");
                    }
                ])->findOrFail($id, ["id_tipo_documento", "numero_documento", "nombres", "apellidos", "direccion", "correo", "sexo", "celular", "fecha_nacimiento", "numero_contacto"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClienteRequest $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);

        $data = $request->validated();
        $cliente->update($data);

        return $cliente;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->delete();
        return $cliente;
    }
}
