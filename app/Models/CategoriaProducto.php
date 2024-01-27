<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProducto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "nombre", "descripcion","id_tipo_categoria"
    ];

    public function tipoCategoria(){
        return $this->hasOne(TipoCategoria::class, "id", "id_tipo_categoria");
    }
}
