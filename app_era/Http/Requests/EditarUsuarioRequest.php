<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarUsuarioRequest extends FormRequest
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
          'name'=> 'required'
          //, 'password'=>'required|confirmed|min:6'
          , 'password'=>'confirmed'
      ];
  }

  public function messages(){
    return [
      'name.required' => "Debe Ingresar un Nombre",
      //'password.min' => "Debe ingresar una contraseña de al menos largo 6",
      'password.confirmed' =>'Las Contraseñas no coinciden'
    ];
  }
}
