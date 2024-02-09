<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VentaRequest extends FormRequest
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
                Rule::unique('ventas', 'correlativo')
                    ->where('id_tipo_comprobante', $this->input('id_tipo_comprobante'))
                    ->where('serie', $this->input('serie')),
            ],
            "id_cliente"=>"nullable|integer|exists:clientes,id",
            "monto_efectivo"=>"required|numeric|max:".$this->input('importe_total'),
            "monto_tarjeta"=>"required|numeric|max:9999999",
            "monto_credito"=>"required|numeric|max:9999999",
            "monto_yape"=>"required|numeric|max:9999999",
            "monto_plin"=>"required|numeric|max:9999999",
            "monto_transferencia"=>"required|numeric|max:9999999",
            "descuento_global"=>"required|numeric|max:9999999",
            "observaciones"=>"nullable|string",
            "id_sucursal"=>"required|integer",
            "fecha_venta"=>"required|date",
            "hora_venta"=>"required|string|size:5",
            "importe_total"=>"required|numeric|min:1|max:99999999",
            "productos"=>"required|array",
            "productos.*.id_producto"=>"required|integer",
            "productos.*.fecha_vencimiento"=>"required|string|size:10",
            "productos.*.lote"=>"nullable|string|max:20",
            "productos.*.cantidad"=>"required|numeric|min:1",
            "productos.*.precio_unitario"=>"required|numeric",
        ];

        if ($this->input('id_cliente') === NULL){
            $requestArray = array_merge($requestArray, [
                "cliente_id_tipo_documento"=>"required|string|size:1",
                "cliente_numero_documento"=>($this->input('cliente_id_tipo_documento') == "0" ? "required" : "nullable")."|string|max:15",
                "cliente_nombres"=>"required|string|max:300",
                "cliente_apellidos"=>"nullable|string|max:300",
                "cliente_direccion"=>"nullable|string|max:400",
                "cliente_celular"=>"nullable|string|max:10",
                "cliente_correo"=>"nullable|string|max:50",
            ]);
        }

        return $requestArray;
    }

    public function messages(): array {
        return [
            'monto_efectivo.max' => 'El valor debe ser menor que el importe total',
            'correlativo.unique' => 'El número de comprobante, serie y correlativo ya existe',
            'id_cliente.exists' => 'El ID del cliente seleccionado no es válido. Consultar con el administrador del sistema.',
            'id_cliente.required' => 'El cliente es obligatorio.',
            'cliente_numero_documento.required' => "El número de documento del cliente es obligatorio.",
            'cliente_nombres.required' => "El nombre/razón social del cliente es obligatorio."
        ];
    }
}
