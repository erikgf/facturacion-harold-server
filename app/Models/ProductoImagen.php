<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoImagen extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_producto', 'numero_imagen', 'img_url'
    ];

    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
