@extends('master', [
  'menuActual' => 'vacacionesProgresivas'])

  @section('contenido')
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
        </div>
      @endif
      <div class="row">
        <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
          {!! Html::link(action("VacacionesController@agregarVacacionesProgresivas")
              , "Registrar Vacaciones Progresivas",["class"=>'btn btn-primary'])  !!}
          <div class="card mt-2">
            <div class="card-header bg-suelditas text-white">Vacaciones Progresivas Registradas</div>
            <div class="card-body">
              <table class="table table-striped table-bordered dataTable no-footer" id="progresivas-table">
                <thead>
                  <tr>
                    <th>Rut de Trabajador</th>
                    <th>Nombre de Trabajador</th>
                    <th>Fecha de Registro</th>
                    <th>Cantidad de Años</th>
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
    <script type="text/javascript" src="{{asset('js/vacaciones_progresivas.index.js')}}"></script>
  @endpush
