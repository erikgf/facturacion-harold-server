<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
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
            "empresa_especial"=>"required|string|size:3",
            "codigo_unico"=>"nullable|string|max:20|unique:productos,codigo_generado",
            "tallas"=>"nullable|string|max:50",
            "nombre"=>"required|string|max:200",
            "descripcion"=>"nullable|string",
            "precio_unitario"=>"required|numeric|min:0",
            "id_unidad_medida"=>"required|string|size:3",
            "id_presentacion"=>"nullable|integer",
            "id_marca"=>"nullable|integer",
            "id_categoria_producto"=>"required|integer",
            "numero_imagen_principal"=>"required|integer",
            "imagenes"=>"nullable|array",
            "imagenes.*"=>"required|image|mimes:jpeg,png,jpg|max:2048",
            "imagenes_indices"=>"nullable|array",
            "imagenes_indices.*"=>"required|integer",
        ];
    }
}
