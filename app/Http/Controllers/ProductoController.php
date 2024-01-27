<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Services\ProductoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $baseRep = asset('');

        $productos = DB::table("productos as pr")
            ->join("categoria_productos as cp", function($q){
                $q->on("cp.id", "=", "pr.id_categoria_producto");
            })
            ->join("marcas as m", function($q){
                $q->on("m.id", "=", "pr.id_marca");
            })
            ->join("unidad_medidas as um", function($q){
                $q->on("um.id", "=", "pr.id_unidad_medida");
            })
            ->whereNull("pr.deleted_at")
            ->select("pr.id", "pr.codigo_generado", "empresa_especial", "pr.nombre as producto", "pr.precio_unitario", "cp.nombre as categoria", "m.nombre as marca", "um.descripcion as unidad_medida")
            ->addSelect(DB::raw("(SELECT CONCAT('".$baseRep."',img_url) FROM producto_imagens WHERE id_producto = pr.id ORDER BY numero_imagen DESC LIMIT 1) as img_url"))
            ->get();

        return $productos;

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        $producto = (new ProductoService)->registrar($data);
        DB::commit();

        return $producto;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $baseRep = asset('');
        $producto = Producto::findOrFail($id);
        $producto->load("imagenes");

        $producto->imagenes_procesadas = $producto->imagenes->map(function($imagen) use ($baseRep){
            return [
                "id"=>$imagen->id,
                "img_url"=>$baseRep.$imagen->img_url,
                "numero_imagen"=>$imagen->numero_imagen
            ];
        });

        $tipoCategoria = CategoriaProducto::findOrFail($producto->id_categoria_producto);
        $producto->id_tipo_categoria = $tipoCategoria->id_tipo_categoria;

        return $producto;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, string $id)
    {
        $producto = Producto::findOrFail($id);
        $data = $request->validated();

        DB::beginTransaction();
        $producto = (new ProductoService)->editar($producto, $data);
        DB::commit();

        return $producto;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        DB::beginTransaction();
        $producto = (new ProductoService)->eliminar($producto);
        DB::commit();

        return $producto;
    }
}
