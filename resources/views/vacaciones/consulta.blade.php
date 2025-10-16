@extends('master', [
  'menuActual' => 'consultaVacaciones'])

  @section('contenido')
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      @include("templates/_mensajes_infos")
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
        </div>
      @endif
      <div class="row">
        <div class="mx-auto col-lg-6 col-md-6 col-sm-12">
          <div class="card">
            <div class="card-header bg-suelditas text-white">
              <span>Crear Vacaciones para Trabajador</span>
            </div>
            <div class="card-body">
                  <div class="form-group{{$errors->has('empleado[]')? ' has-error': ''}}">
                    {!! Form::label('empleado[]', 'Trabajador Seleccionado') !!}
                    {!! Form::select('empleado[]', $empleadosDisponibles
                      ,  null
                      ,['class'=>'form-control selectAutocomplete empleado'
                      , 'title'=>'Seleccione Trabajador'] ) !!}
                      <small class="text-danger">{{$errors->first('empleado[]')}}</small>
                    </div>
                    <div class="btn-group">
                      {!! Form::button('Consultar',['class'=>'btn btn-primary consultar-btn']) !!}
                    </div>
              </div>
            </div>
          </div>
      </div>
      <div class="row d-none">
        <div class="mx-auto col-lg-8 col-md-8 col-sm-12">
          <div class="card mt-2">
            <div class="card-header bg-suelditas text-white">
              <span>Datos del Trabajados</span>
            </div>
            <div class="card-body">
              <ul class="list-group">
                <li class="list-group-item font-weight-bold">RUT
                  <span class="font-weight-normal rut_empleado_span"></span>
                  <span class="font-weight-normal nombre_empleado_span"></span></li>

                <li class="list-group-item font-weight-bold">Fecha de Contrato
                  <span class="font-weight-normal fecha_contrato_span"></span>
                </li>
                <li class="list-group-item font-weight-bold">EMPRESA
                  <span class="font-weight-normal empresa_span">INVERSIONES BOSQUES DEL MAUCO</span>
                </li>
                <li class="list-group-item font-weight-bold">CARGO
                  <span class="font-weight-normal cargo_span"></span>
                </li>

                <li class="list-group-item font-weight-bold">OTROS AÑOS
                  <span class="font-weight-normal progresivas_span"></span>
                </li>

              </ul>
            </div>
          </div>
        </div>
        <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
          <div class="card mt-2">
            <div class="card-header bg-suelditas text-white">Detalle de Vacaciones y Periodos</div>
            <div class="card-body">
              <table class="table table-striped table-bordered dataTable no-footer" id="consulta-table">
                <thead>
                  <tr>
                    <th>Periodo</th>
                    <th>Base</th>
                    <th>Progresivas</th>
                    <th>Tomadas</th>
                    <th>Saldo</th>
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
    <script type="text/javascript" src="{{asset('js/vacaciones.consulta.js')}}"></script>
  @endpush
