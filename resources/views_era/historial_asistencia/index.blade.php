@extends('master', [
'menuActual' => 'historialAsistencia'])

@section('contenido')
<div class="col-lg-12">
  <div class="row">
    <div class="mx-auto col-lg-6 col-md-8 col-sm-12">
      @if(session()->has('resultado'))
      <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
        {{session('resultado')->mensaje}}
      </div>
      @endif
      <div class="card">
        <div class="card-header bg-suelditas text-white">
          <span>Historial de Asistencia de Trabajador</span>
        </div>
        <div class="card-body">
          {!! Form::open(['method'=> 'POST'
          , 'action'=>'HistorialAsistenciaController@generar'
          , 'class' => 'form-horizontal']) !!}
          {!! csrf_field() !!}
          <div class="form-group{{$errors->has('empleado[]')? ' has-error': ''}}">
            {!! Form::label('empleado[]', 'Trabajador Seleccionado') !!}
            {!! Form::select('empleado[]', $empleadosDisponibles
            , isset($rutEmpleado)? $rutEmpleado: null
            ,['class'=>'form-control selectAutocomplete empleado'
            , 'title'=>'Seleccione Trabajador'] ) !!}
            <small class="text-danger">{{$errors->first('empleado[]')}}</small>
          </div>
          <div class="btn-group">
            {!! Form::submit('Ver',['class'=>'btn btn-success btn-historial-asistencia']) !!}
          </div>
          {!! Form::close()!!}
        </div>
      </div>
    </div>
  </div>
  @if(isset($movimientosProcesados))
    <div class="row mt-5 ">
      <div class="mx-auto col-lg-8 col-md-8 col-sm-12">
        <div class="card">
          <div class="card-header bg-suelditas text-white">Movimientos Existentes</div>
          <div class="card-body">
            <table class="table table-striped table-bordered dataTable no-footer" id="movimientos-table">
              <thead>
                <tr>
                  <th>Tipo de Movimiento</th>
                  <th>Fecha de Inicio de Movimiento</th>
                  <th>Fecha de Termino de Movimiento</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($movimientosProcesados as $movProcesado)
                    <tr>
                      <td>{{$movProcesado->tipo_texto}}</td>
                      <td>{{$movProcesado->fecha_inicio_movimiento_proc}}</td>
                       <td>{{$movProcesado->fecha_termino_movimiento_proc}}</td>
                    </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
</div>
@stop
@push('javascript')
    <script type="text/javascript" src="{{asset('js/historial_asistencia.index.js')}}"></script>
@endpush