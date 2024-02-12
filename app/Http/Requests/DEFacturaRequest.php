<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DEFacturaRequest extends FormRequest
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
            "descuento_global"=>"required|numeric|max:9999999",
            "observaciones"=>"nullable|string",
            "fecha_emision"=>"required|date",
            "hora_emision"=>"required|string|size:5",
            "fecha_vencimiento"=>"required|date",
            "id_tipo_moneda"=>"required|string|size:3",
            "importe_total"=>"required|numeric|min:1|max:99999999",
            "productos"=>"required|array",
            "productos.*.id_producto"=>"required|integer",
            "productos.*.cantidad"=>"required|numeric|min:1",
            "productos.*.precio_unitario"=>"required|numeric",
            "condicion_pago"=>"required|string|size:1",
            "es_delivery"=>"required|string|size:1",
        ];

        return $requestArray;
    }

    public function messages(): array {
        return [
            'correlativo.unique' => 'El número de comprobante, serie y correlativo ya existe',
            'id_cliente.exists' => 'El ID del cliente seleccionado no es válido. Consultar con el administrador del sistema.',
            'id_cliente.required' => 'El cliente es obligatorio.'
        ];
    }
}
