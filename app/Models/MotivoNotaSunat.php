<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoNotaSunat extends Model
{
    use HasFactory;
    protected $fillable = ["id_tipo_motivo", "id_tipo_nota", "descripcion"];
    public $incrementing = false;
}
