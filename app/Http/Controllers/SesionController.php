<?php

namespace App\Http\Controllers;

use App\Http\Resources\UsuarioResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class SesionController extends Controller
{
    public function login(Request $request){
        $data = $request->validate([
            "username"=> "required|string|max:20",
            "password"=>"required|string|max:32"
        ]);

        $user = User::where("numero_documento", $data["username"])->first();
        if (!$user){
            throw new \Exception("Usuario no existe", 1);
        }

        if (!Hash::check($data["password"], $user->password)){
            throw new \Exception("Contraseña incorrecta", 1);
        }

        if ($user->estado_activo != 'A'){
            throw new \Exception("Usuario no válido", 1);
        }

        $token = $user->createToken('andreitakidstoken', ['*'], now()->addDay())->plainTextToken;
        return [
            'user'=>new UsuarioResource($user),
            'token'=>$token
        ];
    }

    public function logout(Request $request){
        /*
        $token = PersonalAccessToken::findToken($request->bearerToken());
        if ($token){
            $token->tokenable->tokens()->delete();
        }
        */
        $tokens = $request->user()?->tokens();

        if ($tokens){
            $tokens->delete();
        }

        return [
            "message"=>"Logged out"
        ];
    }
}
