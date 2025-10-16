@extends('master', [
  'menuActual' => 'finiquitos'])

  @section('contenido')
    @include("templates/_modal_confirmacion", ['accion'=>'Anular'
      , 'entidad'=> 'Finiquito'])
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      @include("templates/_mensajes_infos")
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
        </div>
      @endif
      <div class="row">
        <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
          <div class="card">
            <div class="card-header bg-suelditas text-white">Finiquitos Realizados</div>
            <div class="card-body">

              {!! Html::link(action("FiniquitosController@create")
                , "Finiquitar Personal",["class"=>'btn btn-primary mb-5'])  !!}

                <table class="table table-striped table-bordered dataTable no-footer" id="finiquitos-table">
                  <thead>
                    <tr>
                      <th>Rut de Trabajador</th>
                      <th>Nombre de Trabajador</th>
                      <th>Fecha de Contrato</th>
                      <th>Fecha de Finiquito</th>
                      <th>Causa</th>
                      <th>Fecha de Documento</th>
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
        <script type="text/javascript" src="{{asset('js/finiquitos.index.js')}}"></script>
      @endpush
