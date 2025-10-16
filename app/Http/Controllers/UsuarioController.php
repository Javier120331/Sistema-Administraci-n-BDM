<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\EditarUsuarioRequest;
class UsuarioController extends Controller
{

  public function  __construct(){
    $this->middleware('auth');
  }

  public function index(){
    return view('usuarios.index');
  }

  public function getAjaxData(){

     $usuarios= User::habilitados();

     return DataTables::of($usuarios)->make(true);
  }

  public function deshabilitados(){
    return view('usuarios.deshabilitar');
  }

  public function getAjaxDataDeshabilitados(){

     $usuarios= User::deshabilitados();

     return DataTables::of($usuarios)->make(true);
  }

  public function update($id, EditarUsuarioRequest $request){

    $usuario = User::findOrFail($id);

    $usuario->name = Input::get('name');

    if(Input::get('password') !== ""){
      $usuario->password = bcrypt(Input::get('password'));
    }
    $usuario->save();
    return redirect('usuario');
  }

  public function habilitar($id){

      $error = new \stdClass;
      if(Auth::id() == $id){
        $error->ok = false;
        $error->error = 'No es Posible habilitar su propio usuario' ;

      } else {

        $usuario = User::find($id);

        if(!is_null($usuario)){
          $usuario->estado=1;
          $usuario->save();
          $error->ok = true;
        } else {
          $error->error ='El usuario ha habilitar no existe';
          $error->ok = false;
        }
      }
      return json_encode($error);
  }
  public function edit($id){
    $usuario = User::findOrFail($id);
    return view('auth.update', compact('usuario'));
  }

  public function deshabilitar($id){

      $error = new \stdClass;
      if(Auth::id() == $id){
        $error->ok = false;
        $error->error = 'No es Posible deshabilitar su propio usuario' ;

      } else {

        $usuario = User::find($id);

        if(!is_null($usuario)){
          $usuario->estado=0;
          $usuario->save();
          $error->ok = true;
        } else {
          $error->error ='El usuario ha deshabilitar no existe';
          $error->ok = false;
        }
      }
      return json_encode($error);
  }

}
