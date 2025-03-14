<?php

namespace App\Services;

use App\Models\CategoriaProducto;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\TipoCategoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductoService {

    public function listar(){
        $baseRep = asset('');
        $productos = Producto::query()
                        ->join("categoria_productos as cp", "cp.id", "=", "productos.id_categoria_producto")
                        ->join("marcas as m", "m.id", "=", "productos.id_marca")
                        ->join("unidad_medidas as um", "um.id", "=", "productos.id_unidad_medida")
                        ->select(
                                "productos.id",
                                "productos.codigo_generado",
                                "empresa_especial",
                                "productos.nombre as producto",
                                "productos.precio_unitario",
                                "cp.nombre as categoria",
                                "m.nombre as marca",
                                "um.descripcion as unidad_medida",
                                "tallas",
                                DB::raw("(SELECT CONCAT('".$baseRep."',img_url) FROM producto_imagens WHERE id_producto = productos.id ORDER BY numero_imagen DESC LIMIT 1) as img_url")
                            )
                        ->orderBy("productos.nombre")
                        ->get();
        return $productos;
    }

    private function crearCodigoGenerado (Producto $producto){
        $bloqueCategoria = Str::padLeft($producto->id_categoria_producto, 3, '0');

        $dbName = config("database.connections")["mysql"]["database"];
        $posibleNuevoID = DB::table('INFORMATION_SCHEMA.TABLES')
                            ->select('AUTO_INCREMENT as id')
                            ->where('TABLE_SCHEMA', $dbName)
                            ->where('TABLE_NAME','productos')
                            ->pluck("id")
                            ->first();
        $bloqueCodigoProducto = Str::padLeft($posibleNuevoID, 5, '0');

        return  $producto->empresa_especial."-".
                $bloqueCategoria."-".
                $bloqueCodigoProducto;
    }

    private function actualizarCodigoGenerado (Producto $producto){
        $bloqueCategoria = Str::padLeft($producto->id_categoria_producto, 3, '0');
        $bloqueCodigoProducto = Str::padLeft($producto->id, 5, '0');

        return  $producto->empresa_especial."-".
                $bloqueCategoria."-".
                $bloqueCodigoProducto;
    }

    public function registrar(array $data){
        $producto = new Producto;

        $producto->empresa_especial = $data["empresa_especial"];
        $producto->codigo_generado = @$data['codigo_unico']?: null;
        $producto->tallas = @$data['tallas']?: null;
        $producto->nombre = $data["nombre"];
        $producto->descripcion = @$data['descripcion']?: null;
        $producto->precio_unitario = $data["precio_unitario"];
        $producto->id_unidad_medida = $data["id_unidad_medida"];
        $producto->id_presentacion = @$data['id_presentacion']?: 1;
        $producto->id_marca = @$data['id_marca']?: null;
        $producto->id_categoria_producto = $data["id_categoria_producto"];
        $producto->numero_imagen_principal = $data["numero_imagen_principal"];

        if (is_null($producto->codigo_generado)){
            $codigoGenerado = $this->crearCodigoGenerado($producto);
            $producto->codigo_generado = $codigoGenerado;
        }

        $producto->save();

        if (isset($data["imagenes"])){
            for ($i=0; $i < count($data["imagenes"]); $i++) {
                $imageFile = $data["imagenes"][$i];
                $imagenNumero = $data["imagenes_indices"][$i];
                $image = new ProductoImagen();
                $path = $imageFile->store('/imagenes/productos', ['disk'=>'imagenes_productos']);
                $image->img_url = $path;
                $image->id_producto = $producto->id;
                $image->numero_imagen = $imagenNumero;
                $image->save();
            }
        }

        return $producto;
    }

    public function editar(Producto $producto, array $data){
        $producto->empresa_especial = $data["empresa_especial"];
        $producto->codigo_generado = @$data['codigo_unico']?: null;
        $producto->tallas = @$data['tallas']?: null;
        $producto->nombre = $data["nombre"];
        $producto->descripcion = @$data['descripcion']?: null;
        $producto->precio_unitario = $data["precio_unitario"];
        $producto->id_unidad_medida = $data["id_unidad_medida"];
        $producto->id_presentacion = @$data['id_presentacion']?: 1;
        $producto->id_marca = @$data['id_marca']?: null;
        $producto->id_categoria_producto = $data["id_categoria_producto"];
        $producto->numero_imagen_principal = $data["numero_imagen_principal"];

        $producto->update();

        $productoImagenes = ProductoImagen::where(["id_producto"=>$producto->id])->get();
        foreach ($productoImagenes as $imagen) {
            Storage::disk('imagenes_productos')->delete($imagen->img_url);
        }
        ProductoImagen::where(["id_producto"=>$producto->id])->delete();

        if (isset($data["imagenes"])){
            for ($i=0; $i < count($data["imagenes"]); $i++) {
                $imageFile = $data["imagenes"][$i];
                $imagenNumero = $data["imagenes_indices"][$i];
                $image = new ProductoImagen();
                $path = $imageFile->store('/imagenes/productos', ['disk'=>'imagenes_productos']);
                $image->img_url = $path;
                $image->id_producto = $producto->id;
                $image->numero_imagen = $imagenNumero;
                $image->save();
            }
        }

        return $producto;
    }

    public function eliminar(Producto $producto){
        $productoImagenes = ProductoImagen::where(["id_producto"=>$producto->id])->get();
        ProductoImagen::where(["id_producto"=>$producto->id])->delete();

        foreach ($productoImagenes as $imagen) {
            Storage::disk('imagenes_productos')->delete($imagen->img_url);
        }

        $producto->delete();

        return $producto;
    }

    public function actualizarCodigosGenerados(){
        $productos = Producto::all();

        DB::beginTransaction();
        $productos->each(function($producto){
            $codigoGenerado = $this->actualizarCodigoGenerado($producto);
            $producto->codigo_generado = $codigoGenerado;
            $producto->save();
        });
        DB::commit();

        return true;
    }


    public function obtenerProductosCatalogo($pagina, $cadenaBusqueda, $idCategoria, $idTipoCategoria, $idMarca){
        $baseRep = asset('');

        $productos = Producto::query()
                        ->when(!is_null($idCategoria), function($q) use($idCategoria){
                            $q->where("id_categoria_producto", $idCategoria);
                        })
                        ->when(!is_null($idTipoCategoria), function($q) use($idTipoCategoria){
                            $q->whereHas("categoria", fn($q) => $q->where("id_tipo_categoria", $idTipoCategoria));
                        })
                        ->when(!is_null($cadenaBusqueda), function($q) use($cadenaBusqueda){
                            $q->where("nombre", "LIKE", "%".$cadenaBusqueda."%");
                        })
                        ->when(!is_null($idMarca), function($q) use($idMarca){
                            $q->where("id_marca", $idMarca);
                        })
                        ->select(
                            "id",
                            "nombre",
                            DB::raw("CAST(precio_unitario as DECIMAL(10,2)) as precio_unitario"),
                            DB::raw("COALESCE((SELECT CONCAT('{$baseRep}', pi.img_url) as img_url
                                        FROM producto_imagens pi
                                        WHERE pi.id_producto = productos.id AND pi.numero_imagen = productos.numero_imagen_principal),
                                        '../imagenes/productos/default_producto.jpg') as img_url")
                        )
                        ->paginate(20,['*'], 'page', $pagina ?? 1);

        return $productos;
    }


    public function obtenerProductoCatalogoInformacion($id){
        $baseRep = asset('');
        $producto = Producto::query()
                        ->with([
                            "imagenes" => function($q) use ($baseRep){
                                $q->select(
                                    "id_producto",
                                    "numero_imagen",
                                    DB::raw("CONCAT('{$baseRep}', img_url) as img_url")
                                );
                            }
                        ])
                        ->leftJoin("marcas as m", "m.id", "=", "productos.id_marca")
                        ->leftJoin("categoria_productos as c", "c.id", "=", "productos.id_categoria_producto")
                        ->find($id, [
                            "productos.id",
                            "productos.nombre",
                            DB::raw("COALESCE(productos.descripcion, 'Sin descripción') as descripcion"),
                            DB::raw("m.nombre as marca"),
                            DB::raw("c.nombre as categoria"),
                            DB::raw("CAST(precio_unitario AS DECIMAL(10,2)) as precio_unitario")
                        ]);

        return $producto;
    }

    public function obtenerProductoCatalogoUtils(){
        $marcas = Marca::query()->orderBy("nombre")->get(["id", "nombre"]);
        $tipos = TipoCategoria::query()->orderBy("nombre")->get(["id", "nombre"]);
        $categorias = CategoriaProducto::query()->orderBy("nombre")->get(["id", "nombre","id_tipo_categoria"]);

        return [
            "marcas" => $marcas,
            "tipos" => $tipos,
            "categorias" => $categorias
        ];
    }

}