<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearVacacionProgresiva extends FormRequest
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
          'fecha_inicio'=>'required|date',
          'anios_empresa'=>'required|integer|gte:10'
      ];
    }
    public function messages()
    {
      return [
        'fecha_inicio.required' => 'Debe Ingresar Fecha de Inicio',
        'anios_empresa.required'  => 'Debe Ingresar los años de experiencia en otra empresa',
        "anios_empresa.integer" => 'Los años de experiencia deben ser un valor numérico',
        "anios_empresa.gte" => 'Años de experiencia debe ser mayor o igual a 10'
      ];
    }
}
