<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DENotaRequest extends FormRequest
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
        $requestArray = [
            "id_tipo_comprobante"=>"required|string|size:2",
            "serie"=>"required|string|size:4",
            "correlativo"=>[
                "required",
                "integer",
                Rule::unique('documento_electronicos', 'correlativo')
                    ->where('id_tipo_comprobante', $this->input('id_tipo_comprobante'))
                    ->where('serie', $this->input('serie')),
            ],
            "id_cliente"=>"nullable|integer|exists:clientes,id",
            "observaciones"=>"nullable|string",
            "fecha_emision"=>"required|date",
            "hora_emision"=>"required|string|size:5",
            "fecha_vencimiento"=>"required|date",
            "id_tipo_moneda"=>"required|string|size:3",
            "importe_total"=>"required|numeric|min:1|max:99999999",
            "id_tipo_comprobante_modifica"=>"required|string|size:2",
            "serie_comprobante_modifica"=>"required|string|size:4",
            "correlativo_comprobante_modifica"=>[
                "required",
                "integer",
                Rule::exists('documento_electronicos', 'correlativo')
                    ->where('id_tipo_comprobante', ["01","03"])
                    ->where("estado_cdr", "0"),
            ],
            "id_tipo_motivo_nota"=>"required|string|size:2",
            "descripcion_motivo_nota"=>"required|string|max:400",
            "productos"=>"required|array",
            "productos.*.id_producto"=>"required|integer",
            "productos.*.cantidad"=>"required|numeric|min:1",
            "productos.*.precio_unitario"=>"required|numeric",
        ];

        return $requestArray;
    }

    public function messages(): array {
        return [
            'correlativo.unique' => 'El número de comprobante, serie y correlativo ya existe',
            'id_cliente.exists' => 'El ID del cliente seleccionado no es válido. Consultar con el administrador del sistema.',
            'id_cliente.required' => 'El cliente es obligatorio.',
            'correlativo_comprobante_modifica.exists' => 'El número de comprobante a modificar, debe existir y estar en SUNAT.'
        ];
    }
}
