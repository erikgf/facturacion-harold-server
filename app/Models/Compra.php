<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;

    public function proveedor(){
        return $this->hasOne(Proveedor::class, "id", "id_proveedor");
    }

    public function detalle(){
        return $this->hasMany(CompraDetalle::class, "id_compra", "id");
    }
}
