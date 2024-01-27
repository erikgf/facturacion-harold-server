<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
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
            "id_tipo_documento"=> "required|string|size:1",
            "numero_documento"=>"nullable|string|max:15",
            "nombres"=>"required|string|max:300",
            "apellidos"=>"nullable|string|max:300",
            "direccion"=>"nullable|string|max:400",
            "correo"=>"nullable|string|max:50",
            "sexo"=>"nullable|string|size:1",
            "celular"=>"nullable|string|max:10",
            "fecha_nacimiento"=>"nullable|date",
            "numero_contacto"=>"nullable|string|max:20",
        ];
    }
}
