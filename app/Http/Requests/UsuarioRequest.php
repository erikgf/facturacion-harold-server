<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "numero_documento"=>"required|string|size:8",
            "nombres_apellidos"=>"required|string|max:350",
            "celular"=>"nullable|string|max:15",
            "fecha_nacimiento"=>"nullable|date",
            "fecha_ingreso"=>"nullable|date",
            "id_rol"=>"required|integer",
            "sexo"=>"required|string|size:1",
            "email"=>"required|string|max:255",
            "acceso_sistema"=>"required|boolean",
            "estado_activo"=>"required|string|size:1",
        ];
    }
}
