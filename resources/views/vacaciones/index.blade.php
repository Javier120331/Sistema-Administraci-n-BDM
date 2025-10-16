@extends('master', [
  'menuActual' => 'vacaciones'])

  @section('contenido')
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      @include("templates/_mensajes_infos")
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
        </div>
      @endif
      <div class="row">
        <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
          {!! Html::link(action("VacacionesController@create")
              , "Registrar Vacaciones",["class"=>'btn btn-primary'])  !!}
          <div class="card mt-2">
            <div class="card-header bg-suelditas text-white">Vacaciones Registradas para el Personal</div>
            <div class="card-body">
              <table class="table table-striped table-bordered dataTable no-footer" id="vacaciones-table">
                <thead>
                  <tr>
                    <th>Rut de Trabajador</th>
                    <th>Nombre de Trabajador</th>
                    <th>Fecha de Registro</th>
                    <th>Fecha de Inicio</th>
                    <th>Cantidad de Días</th>
                    <th>Fecha de Termino</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  @stop
  @push('javascript')
    <script type="text/javascript" src="{{asset('js/vacaciones.index.js')}}"></script>
  @endpush
