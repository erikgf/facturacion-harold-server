<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaProductoRequest;
use App\Models\CategoriaProducto;

class CategoriaProductoController extends Controller
{
    //
    public function index()
    {
        return CategoriaProducto::with(["tipoCategoria" => function($query){
            $query->select("id", "nombre");
        }])
        ->orderBy("nombre")
        ->get(["id", "nombre", "descripcion", "id_tipo_categoria"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoriaProductoRequest $request)
    {
        $data = $request->validated();

        $categoriaProducto = new CategoriaProducto;
        $categoriaProducto->nombre = $data["nombre"];
        $categoriaProducto->descripcion = $data["descripcion"];
        $categoriaProducto->id_tipo_categoria = $data["id_tipo_categoria"];
        $categoriaProducto->save();

        return $categoriaProducto;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categoriaProducto = CategoriaProducto::with(["tipoCategoria" => function($query){
                            $query->select("id", "nombre");
                        }])->findOrFail($id, ["id", "nombre","descripcion", "id_tipo_categoria"]);

        return $categoriaProducto;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoriaProductoRequest $request, string $id)
    {
        $categoriaProducto = CategoriaProducto::findOrFail($id);
        $data = $request->validated();
        $categoriaProducto->update($data);

        return $categoriaProducto;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categoriaProducto = CategoriaProducto::findOrFail($id);
        $categoriaProducto->delete();
        return $categoriaProducto;
    }

    public function obtenerPorTipo(string $tipo)
    {
        return CategoriaProducto::where(["id_tipo_categoria"=>$tipo])
                    ->orderBy("nombre")
                    ->get(["id", "nombre", "descripcion", "id_tipo_categoria"]);
    }
}
