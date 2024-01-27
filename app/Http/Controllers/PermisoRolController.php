<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\PermisoRol;
use App\Models\Rol;
use App\Services\PermisoRolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoRolController extends Controller
{
    public function index(string $idRol)
    {
        Rol::findOrFail($idRol);

        $permisosActivos = DB::table("permisos as p")
            ->join("permiso_rols as pr", function($join){
                $join->on("pr.id_permiso","=","p.id");
            })
            ->join("permisos as p2", function($join){
                $join->on("p.id_permiso_padre", "=","p2.id");
            })
            ->where(["pr.id_rol"=>$idRol, "p.es_menu_interfaz"=>1, "p.estado"=>"A"])
            ->orderBy("p2.id")
            ->orderBy("p.orden")
            ->select("p.id","p.titulo_interfaz", "p2.titulo_interfaz as superior", "p.orden")
            ->get();

        $permisosInactivos = DB::table("permisos as p")
            ->join("permisos as p2", function($join){
                $join->on("p.id_permiso_padre", "=","p2.id");
            })
            ->whereNotIn("p.id",  function($q) use($idRol) {
                $q->select('id_permiso')->from('permiso_rols')->where(["id_rol"=>$idRol]);
            })
            ->where(["p.es_menu_interfaz"=>1, "p.estado"=>"A"])
            ->orderBy("p2.id")
            ->orderBy("p.orden")
            ->select("p.id","p.titulo_interfaz", "p2.titulo_interfaz as superior", "p.orden")
            ->get();

        return ["permisosActivos"=>$permisosActivos, "permisosInactivos"=>$permisosInactivos];
    }

    public function agregarPermiso(Request $request){
        $data = $request->validate([
            "id_rol"=>"required|integer",
            "id_permiso"=>"required|integer"
        ]);

        $idRol = $data["id_rol"];
        $idPermiso = $data["id_permiso"];

        return (new PermisoRolService)->agregarPermiso($idRol, $idPermiso);
    }

    public function quitarPermiso(Request $request){
        $data = $request->validate([
            "id_rol"=>"required|integer",
            "id_permiso"=>"required|integer"
        ]);

        $idRol = $data["id_rol"];
        $idPermiso = $data["id_permiso"];

        return (new PermisoRolService)->quitarPermiso($idRol, $idPermiso);
    }

    public function obtenerPermisos(Request $request){
        $usuario = $request->user();

        if (!$usuario){
            throw new \Exception("No hay usuario válido", 401);
        }

        $idRol = $usuario->id_rol;

        $rol = Rol::with(["permisos"=>function($q){
            $q->select("id");
            $q->where(["permiso_rols.estado"=>'A']);
            $q->where(["permisos.estado"=>'A']);
            $q->whereNull("id_permiso_padre");
        }])
        ->findOrFail($idRol, ["id", "nombre"]);

        $permisoMenu = [];

        foreach ($rol->permisos as $key => $permisoPadre) {
            $permiso = Permiso::with(["hijos"=>function($q) use ($idRol){
                            $q->join("permiso_rols as pr", "pr.id_permiso", "=", "id");
                            $q->select("id", "es_menu_interfaz", "titulo_interfaz", "url", "icono_interfaz", "id_permiso_padre");
                            $q->where(["permisos.estado"=>'A', "pr.id_rol" => $idRol]);
                            $q->orderBy("orden");
                        }])
                        ->find($permisoPadre->id, ["id", "es_menu_interfaz", "titulo_interfaz", "url", "icono_interfaz", "id_permiso_padre"]);

            array_push($permisoMenu, $permiso);
        }

        return $permisoMenu;
    }
}
