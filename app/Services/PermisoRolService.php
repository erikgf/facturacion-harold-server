<?php

namespace App\Services;

use App\Models\Permiso;
use App\Models\PermisoRol;
use App\Models\Rol;

class PermisoRolService {

    public function agregarPermiso(string $idRol, string $idPermiso){
        Rol::findOrFail($idRol);
        $permiso = Permiso::findOrFail($idPermiso);

        $permisoRol = new PermisoRol;

        $permisoRol->id_rol = $idRol;
        $permisoRol->id_permiso = $idPermiso;
        $permisoRol->estado = 'A';

        $permisoRol->save();
        return $permiso;
    }

    public function quitarPermiso(string $idRol, string $idPermiso){
        Rol::findOrFail($idRol);
        $permiso = Permiso::findOrFail($idPermiso);
        $permisoRol = PermisoRol::where(["id_rol"=>$idRol, "id_permiso"=>$idPermiso])->first();
        $permisoRol->forceDelete();

        return $permiso;
    }
}
