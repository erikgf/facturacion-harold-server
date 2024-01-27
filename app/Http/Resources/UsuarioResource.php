<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          "nombresApellidos"=>$this->nombres_apellidos,
          "idRol"=>$this->id_rol,
          "numeroDocumento"=>$this->numero_documento
        ];
    }
}
