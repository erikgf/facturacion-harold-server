<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductoService {

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
            var_dump($codigoGenerado);
        });
        DB::commit();

        return true;
    }

}


