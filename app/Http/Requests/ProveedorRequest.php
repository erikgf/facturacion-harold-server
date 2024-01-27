<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProveedorRequest extends FormRequest
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
            "razon_social"=>"required|string|max:250",
            "direccion"=>"nullable|string|max:250",
            "correo"=>"nullable|string|max:100",
            "nombre_contacto"=>"nullable|string|max:250",
            "celular_contacto"=>"nullable|string|max:15",
        ];
    }

}
