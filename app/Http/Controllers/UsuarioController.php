<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use App\Services\UsuarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        return User::with([
                        "rol" => function($query){
                            return $query->select("id", "nombre");
                        }
                    ])->get(["id", "numero_documento", "nombres_apellidos", "celular", "id_rol", "email", "sexo", "celular", "fecha_nacimiento", "fecha_ingreso","acceso_sistema", "estado_activo"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsuarioRequest $request)
    {
        $data = $request->validated();

        $cliente = new User;

        $cliente->numero_documento = $data["numero_documento"];
        $cliente->nombres_apellidos = $data["nombres_apellidos"];
        $cliente->celular = $data["celular"];
        $cliente->email = $data["email"];
        $cliente->sexo = $data["sexo"];
        $cliente->id_rol = $data["id_rol"];
        $cliente->fecha_nacimiento = $data["fecha_nacimiento"];
        $cliente->fecha_ingreso = $data["fecha_ingreso"];
        $cliente->acceso_sistema = $data["acceso_sistema"];
        $cliente->estado_activo = $data["estado_activo"];
        $cliente->password = Hash::make($data["numero_documento"]);

        return $cliente;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return User::with([
                    "rol" => function($query){
                        return $query->select("id", "nombre");
                    }
                ])->findOrFail($id, ["numero_documento", "nombres_apellidos", "celular", "id_rol", "email", "sexo", "celular", "fecha_nacimiento", "fecha_ingreso","acceso_sistema", "estado_activo"]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsuarioRequest $request, string $id)
    {
        $cliente = User::findOrFail($id);

        $data = $request->validated();
        $cliente->update($data);

        return $cliente;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = User::findOrFail($id);

        $cliente->delete();
        return $cliente;
    }

    public function obtenerParaSeleccionar()
    {
        return User::get(["id", "numero_documento", "nombres_apellidos"]);
    }


    public function cambiarClave(Request $request)
    {

        $data = $request->validate([
            "nueva_clave"=>"required|string|max:255",
            "id_usuario"=>"required|integer"
        ]);

        $idUsuario = $data["id_usuario"];
        $nuevaClave  = $data["nueva_clave"];

        $usuario = User::findOrFail($idUsuario);
        (new UsuarioService)->cambiarClave($usuario, $nuevaClave);

        return $idUsuario;
    }
}
