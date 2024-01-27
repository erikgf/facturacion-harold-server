<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permiso extends Model
{
    use HasFactory;

    protected $fillable = [
        "es_menu_interfaz", "titulo_interfaz", "url", "icono_interfaz", "id_permiso_padre", "orden", "estado"
    ];

    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'permiso_rols', 'id_permiso', 'id_rol');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id_permiso_padre');
    }

    public function parentRecursive(): BelongsTo
    {
        return $this->parent()->with('parentRecursive');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(self::class, 'id_permiso_padre');
    }

    /**
     * This will give model's Children, Children's Children and so on until last node.
     * @return HasMany
     */
    public function hijosRecursivo(): HasMany
    {
        return $this->children()->with('hijosRecursivo');
    }

}
