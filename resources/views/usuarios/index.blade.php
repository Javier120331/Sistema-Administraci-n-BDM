@extends('master', [
  'menuActual' => 'usuarios'])

@section('contenido')
    @include('templates._modal_acciones',[
        'entidad'=>'Usuario',
        'accion' => 'Deshabilitar'
    ])
    <div class="col-md-12 col-lg-12 col-xs-12">
      <a href="{{ url('/register') }}" class="btn btn-success">
                                  Crear Nuevo Usuario
                              </a>
      {{ Html::link(action('UsuarioController@deshabilitados')
          , 'Ver Deshabilitados'
          , ['class' =>'btn btn-primary']) }}

    </div>
    <div class="col-md-12 col-lg-12 col-xs-12">
      <div class="pt-5">
        <div class="card">
          <div class="card-header bg-suelditas text-white">Usuarios Existentes</div>
          <div class="card-body">
            <table class="table table-striped table-bordered dataTable no-footer" id="usuarios-table">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Correo</th>
                  <th>Fecha de Creación</th>
                  <th>Fecha de Última Modificación</th>
                  <th>Acciones</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    @stop

    @push('javascript')
        <script type="text/javascript" src="{{asset('js/usuario.index.js')}}">
        </script>
    @endpush
