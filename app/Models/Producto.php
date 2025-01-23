<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_especial', 'tallas', 'nombre', 'descripcion', 'precio_unitario',
        'id_unidad_medida', 'id_presentacion', 'id_marca', 'id_categoria_producto',
        'numero_imagen_principal'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function imagenes(){
        return $this->hasMany(ProductoImagen::class, 'id_producto');
    }

    public function marca(){
        return $this->hasOne(Marca::class, 'id', 'id_marca');
    }

    public function unidadMedida(){
        return $this->hasOne(UnidadMedida::class, 'id', 'id_unidad_medida');
    }

    public function categoria(){
        return $this->hasOne(CategoriaProducto::class, 'id', 'id_categoria_producto');
    }

}
