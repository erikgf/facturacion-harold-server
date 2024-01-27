<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    public function cliente(){
        return $this->hasOne(Cliente::class, "id", "id_cliente");
    }

    public function detalle(){
        return $this->hasMany(VentaDetalle::class, "id_venta", "id");
    }

    public function pagos(){
        return $this->hasMany(VentaPago::class,"id","id_venta");
    }

    public function credito(){
        return $this->hasOne(Venta::class,"id","id_venta");
    }

    public function comprobante(){
        return $this->belongsTo(DocumentoElectronico::class, "id", "id_atencion");
    }
}
