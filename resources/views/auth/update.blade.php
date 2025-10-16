@extends('master', [
  'menuActual' => 'editarUsuario'])

@section('contenido')

        <div class="mx-auto col-md-8 col-lg-6 col-sm-12 ">
            <div class="card">
                <div class="card-header text-white bg-suelditas">Editar Usuario</div>
                <div class="card-body">
                  @if(isset($mensajeCorrecto))
                    <div class="alert alert-success" role="alert">{{$mensajeCorrecto}}</div>
                  @endif
                  {!! Form::model($usuario, ['method' => 'PATCH'
                      , 'action' => ['UsuarioController@update', $usuario->id]
                      , 'class' => 'form-horizontal']) !!}
                      {!! csrf_field() !!}

                      @include('templates._usuario_form_edit', [
                        'textoBoton'=> 'Actualizar'
                      ])
                    {!! Form::close() !!}

                </div>
            </div>
    </div>
@endsection
