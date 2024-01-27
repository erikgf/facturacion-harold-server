<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "nombre"
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'permiso_rols', 'id_rol', 'id_permiso');
    }
}
