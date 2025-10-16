<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearMovimientoAsistenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fecha_inicio'=>'required|date_format:"d/m/Y"',
            'cantidad_dias'=>'required|integer|gte:1'
        ];
    }

    public function messages()
    {
      return [
        'fecha_inicio.required' => 'Debe Ingresar Fecha de Inicio',
        'cantidad_dias.required'  => 'Debe Ingresar una Cantidad',
        "cantidad_dias.integer" => 'La cantidad debe ser un valor numérico',
        "cantidad_dias.gte" => 'Cantidad debe ser mayor a 1'
      ];
    }
}
