@extends('master', [
  'menuActual' => 'ingresarMovimientos'])

  @section('contenido')
    <div class="mx-auto col-lg-6 col-md-8 col-sm-12">
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
          @if(session("resultado")->estado && session('resultado')->descargable!= 0)
            <a href='{{ action("MovimientosController@download"
              ,["id"=>session("resultado")->idDocumento])}}' target="_blank" class="btn btn-danger float-right" ><i class="far fa-file-pdf"></i> Descargar PDF</a>
        @endif
        </div>
      @endif
      <div class="card">
        <div class="card-header bg-suelditas text-white">
          <span>Ingresar Movimientos</span>
        </div>
        <div class="card-body">
          {!! Form::open(['method'=> 'POST'
                  , 'action'=>'MovimientosController@store'
                  , 'class' => 'form-horizontal']) !!}
              {!! csrf_field() !!}
              <div class="form-group{{$errors->has('empleado[]')? ' has-error': ''}}">
                {!! Form::label('empleado[]', 'Trabajador Seleccionado') !!}
                {!! Form::select('empleado[]', $empleadosDisponibles
                  ,  old('empleado[]')
                  ,['class'=>'form-control selectAutocomplete empleado'
                  , 'title'=>'Seleccione Trabajador'] ) !!}
                  <small class="text-danger">{{$errors->first('empleado[]')}}</small>
              </div>
              <div class="form-group{{$errors->has('tipo')? ' has-error': ''}}">
                {!! Form::label('tipo', 'Tipo') !!}
                {!! Form::select('tipo', $tiposMovimientos ,old('tipo'), ['class'=>'form-control tipo_movimiento_select']) !!}
              </div>
              <div class="form-group{{$errors->has('fecha_inicio')? ' has-error': ''}}">
                  {!! Form::label('fecha_inicio', 'Fecha de Inicio') !!}
                  <div class="input-group date selectorSoloFechaVinculado1" id="fecha_inicio_dp" data-target-input="nearest">
                    {!! Form::text('fecha_inicio',Carbon\Carbon::now()->format('d/m/Y')
                      ,  ['class'=>'fecha_inicio form-control datetimepicker-input'
                      , 'data-target'=>'#fecha_inicio_dp'] ) !!}
                    <div class="input-group-append" data-target="#fecha_inicio_dp" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                  <small class="text-danger">{{$errors->first('fecha_inicio')}}</small>
              </div>

              <div class="{{old('tipo') != null && old('tipo') == 'AT'? 'd-none': '' }}div_cantidad form-group{{$errors->has('cantidad_dias')? ' has-error': ''}}">
                  {!! Form::label('cantidad_dias', 'Cantidad de Días') !!}
                  {!! Form::number("cantidad_dias", 1, ['class'=>'form-control cantidad_dias']) !!}
                  <small class="text-danger">{{$errors->first('cantidad_dias')}}</small>
              </div>

              <div class="{{old('tipo') != null && old('tipo') == 'AT'? 'd-none': '' }}div_cantidad form-group{{$errors->has('fecha_final')? ' has-error': ''}}">
                  {!! Form::label('fecha_final', 'Fecha Final') !!}
                  {!! Form::text("fecha_final", Carbon\Carbon::now()->format('d/m/Y'), ['class'=>'form-control fecha_final'
                    , 'disabled'=>'true']) !!}
                  <small class="text-danger">{{$errors->first('fecha_final')}}</small>
              </div>

              <div class="{{old('tipo') != null && old('tipo') == 'AT'? '': 'd-none' }} div_hora form-group{{$errors->has('hora_entrada')? ' has-error': ''}}">
                  {!! Form::label('hora_entrada', 'Hora de Entrada') !!}
                  <div class="input-group date selectorSoloHora" id="hora_entrada_dp" data-target-input="nearest" >
                    {!! Form::text('hora_entrada',"08:00"
                      ,  ['class'=>'hora_entrada form-control datetimepicker-input'
                      , 'data-target'=>'#hora_entrada_dp'] ) !!}
                    <div class="input-group-append" data-target="#hora_entrada_dp" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="far fa-clock"></i></div>
                    </div>
                  </div>
                  <small class="text-danger">{{$errors->first('hora_entrada')}}</small>
              </div>
              <div class="{{old('tipo') != null && old('tipo') == 'AT'? '': 'd-none' }} div_hora form-group{{$errors->has('hora_llegada')? ' has-error': ''}}">
                  {!! Form::label('hora_llegada', 'Hora de Llegada') !!}
                  <div class="input-group date selectorSoloHora" id="hora_llegada_dp" data-target-input="nearest" >
                    {!! Form::text('hora_llegada',"08:00"
                      ,  ['class'=>'hora_llegada form-control datetimepicker-input'
                      , 'data-target'=>'#hora_llegada_dp'] ) !!}
                    <div class="input-group-append" data-target="#hora_llegada_dp" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="far fa-clock"></i></div>
                    </div>
                  </div>
                  <small class="text-danger">{{$errors->first('hora_llegada')}}</small>
              </div>
              <div class="btn-group">
                  {!! Form::submit('Ingresar',['class'=>'btn btn-success']) !!}
              </div>
          {!! Form::close()!!}
        </div>
      </div>
    </div>
  @stop
  @push('javascript')
    <script type="text/javascript" src="{{asset('js/movimientos.ingresar.js')}}"></script>
  @endpush
