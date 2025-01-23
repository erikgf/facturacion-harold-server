<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        "id_tipo_documento", "numero_documento", "razon_social", "direccion", "correo"
    ];

    public function tipoDocumento(){
        return $this->hasOne(TipoDocumento::class, "id", "id_tipo_documento");
    }
}
