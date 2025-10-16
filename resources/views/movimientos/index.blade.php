@extends('master', [
  'menuActual' => 'movimientos'])

  @section('contenido')
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      @include("templates/_mensajes_infos")
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
          @if(session("resultado")->estado && session('resultado')->idDocumento!= -1)
            <a href='{{ action("MovimientosController@download"
              ,["id"=>session("resultado")->idDocumento])}}' class="btn btn-danger float-right" ><i class="far fa-file-pdf"></i> Descargar PDF</a>
        @endif
        </div>
      @endif
      <div class="row">
        <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
          <div class="card">
            <div class="card-header bg-suelditas text-white">Movimientos Existentes</div>
            <div class="card-body">
              <table class="table table-striped table-bordered dataTable no-footer" id="movimientos-table">
                <thead>
                  <tr>
                    <th>Rut de Trabajador</th>
                    <th>Nombre de Trabajador</th>
                    <th>Tipo de Movimiento</th>
                    <th>Area</th>
                    <th>Fecha de Inicio de Movimiento</th>
                    <th>Fecha de Termino de Movimiento</th>
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
    <script type="text/javascript" src="{{asset('js/movimientos.ingresar.js')}}"></script>
  @endpush
