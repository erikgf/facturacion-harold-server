<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoElectronicoDetalle extends Model
{
    use HasFactory;
    public function producto(){
        return $this->hasOne(Producto::class, "id", "id_producto");
    }
}
