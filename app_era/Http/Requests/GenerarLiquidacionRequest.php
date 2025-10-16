<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerarLiquidacionRequest extends FormRequest
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
      /*  "cosechador" =>"required_without:seleccionarTodos, grupo"
      , "grupo" => "required_without:seleccionarTodos"*/

      ];
  }

  public function messages(){
    return ["cosechador.required_without" => "Debe Seleccionar Cosechadores"
          , "grupo.required_without" => "Debe Seleccionar un Grupo"];
  }
}
