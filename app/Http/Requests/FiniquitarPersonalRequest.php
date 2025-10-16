<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiniquitarPersonalRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public static function rules()
    {
      return [
          'fecha_finiq'=>'required|date_format:"d/m/Y"',
        //  'causaFiniquito'=>'required|integer|gte:10'
        'causaFiniquito'=>'required',
        'promedio_sueldo'=> 'required',
        'anios_servicio_total'=> 'required',
        'dias_vacaciones_txt'=> 'required',
        'total_dias_vacaciones_txt'=>'required'
      ];
    }
    public static function messages()
    {
      return [
        'fecha_finiq.date_format' => 'El formato de la fecha es incorrecto',
        'fecha_finiq.required' => 'Debe Ingresar una Fecha de Finiquito',
        'causaFiniquito.required'  => 'Debe Ingresar la causa del Finiquito',
        "promedio_sueldo.required" => 'Debe ingresar una remuneración promedio',
        "anios_servicio_total.required" => 'Debe ingresar total de años de servicio',
        "dias_vacaciones_txt.required"=> "Debe indicar cantidad de días de vacaciones",
        'total_dias_vacaciones_txt.required'=> "El total por concepto de dias de vacaciones es requerido",
      ];
    }
}
