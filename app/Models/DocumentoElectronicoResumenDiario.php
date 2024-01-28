<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoElectronicoResumenDiario extends Model
{
    use HasFactory, SoftDeletes;

    public function detalle(){
        return $this->hasMany(DocumentoElectronicoResumenDiarioDetalle::class, "id_documento_electronico_resumen_diario", "id");
    }
}
