@extends('master', [
  'menuActual' => 'usuariosDeshabilitados'])

@section('contenido')
    @include('templates._modal_acciones',[
        'entidad'=>'Usuario',
        'accion' => 'Habilitar'
    ])
    <div class="col-md-12 col-lg-12 col-xs-12">
      <div class="pt-5">
        <div class="card">
          <div class="card-header bg-suelditas text-white">Usuarios Deshabilitados</div>
          <div class="card-body">
            <table class="table table-striped table-bordered dataTable no-footer"
                  id="usuarios-table">
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
        <script type="text/javascript"
        src="{{asset('js/usuario.deshabilitar.js')}}">
        </script>
    @endpush
