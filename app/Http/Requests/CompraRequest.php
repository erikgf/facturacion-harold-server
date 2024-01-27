<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompraRequest extends FormRequest
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
            "numero_comprobante"=>"required|string|max:20",
            "id_tipo_comprobante"=>"required|string|size:2",
            "id_proveedor"=>"required|integer",
            "tipo_pago"=>"required|string|size:1",
            "tipo_tarjeta"=>"nullable|string|size:1",
            "observaciones"=>"nullable|string",
            "guias_remision"=>"nullable|string",
            "id_sucursal"=>"required|integer",
            "fecha_compra"=>"required|date",
            "hora_compra"=>"required|string|size:5",
            "importe_total"=>"required|numeric",
            "productos"=>"required|array",
            "productos.*.id_producto"=>"required|integer",
            "productos.*.cantidad"=>"required|numeric|min:1",
            "productos.*.precio_unitario"=>"required|numeric",
            "productos.*.lote"=>"nullable|string|max:20"
        ];
    }
}
