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
        return (new ProductoService)->listar();

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

    public function getTicketsData(Request $request){
        $data = $request->validate([
            "items"=> "required|array",
            "items.*.id"=>"required|integer",
            "items.*.cantidad"=>"required|integer"
        ]);


        $items = $data["items"];
        $collectionItems = collect($items)->sortBy('id');

        $collectionIds = $collectionItems->map(function($item){
            return $item["id"];
        });

        $productos = Producto::with(["categoria"=> function($q){
                            $q->select("id", "nombre");
                        }, "marca"=>function($q){
                            $q->select("id", "nombre");
                        }])
                        ->whereIn("id", $collectionIds->toArray())
                        ->orderBy("id")
                        ->get([
                            "id","id_marca", "id_categoria_producto", "codigo_generado", "empresa_especial", "tallas", "nombre", "precio_unitario"
                        ]);

        $productos->each(function($p,$i) use($items){
            $p->veces = (int) $items[$i]["cantidad"];
            return $p;
        });

        return $productos;
    }

    public function obtenerProductosCatalogo(Request $request){
        $data = $request->validate([
            "id_categoria"=>"nullable|integer",
            "id_tipo_categoria"=>"nullable|integer",
            "id_marca" => "nullable|integer",
            "q" => "nullable|string|max:300",
            "page"=>"nullable|integer"
        ]);

        return (new ProductoService)->obtenerProductosCatalogo(
            pagina : @$data["page"],
            cadenaBusqueda: @$data["q"],
            idCategoria: @$data["id_categoria"],
            idTipoCategoria: @$data["id_tipo_categoria"],
            idMarca: @$data["id_marca"]
        );
    }

    public function obtenerProductoCatalogoInformacion(int $id){
        return (new ProductoService)->obtenerProductoCatalogoInformacion($id);
    }

    public function obtenerProductoCatalogoUtils(){
        return (new ProductoService)->obtenerProductoCatalogoUtils();
    }

}
