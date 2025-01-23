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
        if ($this->isMethod("PUT")) {
            $id = $this->input("cliente");
            return [
                "id_tipo_documento"=> "required|string|size:1",
                "numero_documento"=>($this->input('id_tipo_documento') == "0" ? "required" : "nullable")."|string|max:".($this->input("id_tipo_documento") == "1" ? "8" : "11"),
                "nombres"=>"required|string|max:300",
                "apellidos"=>"nullable|string|max:300",
                "direccion"=>"nullable|string|max:400",
                "correo"=>"nullable|string|max:50|unique:clientes,correo,except,".$id,
                "sexo"=>"nullable|string|size:1",
                "celular"=>"nullable|string|max:10|unique:clientes,celular,except,".$id,
                "fecha_nacimiento"=>"nullable|date",
                "numero_contacto"=>"nullable|string|max:20",
            ];
        }

        return [
            "id_tipo_documento"=> "required|string|size:1",
            "numero_documento"=>($this->input('id_tipo_documento') == "0" ? "required" : "nullable")."|string|max:".($this->input("id_tipo_documento") == "1" ? "8" : "11"),
            "nombres"=>"required|string|max:300",
            "apellidos"=>"nullable|string|max:300",
            "direccion"=>"nullable|string|max:400",
            "correo"=>"nullable|string|max:100|unique:clientes,correo",
            "sexo"=>"nullable|string|size:1",
            "celular"=>"nullable|string|max:10|unique:clientes,celular",
            "fecha_nacimiento"=>"nullable|date",
            "numero_contacto"=>"nullable|string|max:20",
        ];
    }

    public function messages(): array {
        return [
            'numero_documento.required' => "El número de documento del cliente es obligatorio.",
            'nombres.required' => "El nombre/razón social del cliente es obligatorio."
        ];
    }
}
