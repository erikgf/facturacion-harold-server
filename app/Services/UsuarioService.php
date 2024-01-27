<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioService {

    public function cambiarClave(User $usuario, string $nuevaClave){
        $usuario->password = Hash::make($nuevaClave);
        $usuario->save();

        return $usuario;
    }
}
