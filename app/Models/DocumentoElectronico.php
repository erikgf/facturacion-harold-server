<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoElectronico extends Model
{
    use HasFactory, SoftDeletes;

    public function detalle(){
        return $this->hasMany(DocumentoElectronicoDetalle::class, "id_documento_electronico", "id");
    }

    public function cuotas(){
        return $this->hasMany(DocumentoElectronicoCuota::class, "id_documento_electronico", "id");
    }
}
