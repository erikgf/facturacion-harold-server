<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerieDocumento extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "id_tipo_comprobante", "serie", "correlativo", "id_sucursal"
    ];

    public function tipoComprobante(){
        return $this->hasOne(TipoComprobante::class, "id" , "id_tipo_comprobante");
    }

    public function sucursal(){
        return $this->hasOne(Sucursal::class, "id" , "id_sucursal");
    }
}
