@extends('master', [
  'menuActual' => 'ingresarProgresivas'])

  @section('contenido')
    <div class="mx-auto col-lg-6 col-md-8 col-sm-12">
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
          
          </div>
        @endif
      <div class="card">
        <div class="card-header bg-suelditas text-white">
          <span>Ingresar Vacaciones Progresivas</span>
        </div>
        <div class="card-body">
          {!! Form::open(['method'=> 'POST'
                  , 'action'=>'VacacionesController@storeVacacionesProgresivas'
                  , 'class' => 'form-horizontal']) !!}
              {!! csrf_field() !!}
              <div class="form-group{{$errors->has('empleado[]')? ' has-error': ''}}">
                {!! Form::label('empleado[]', 'Trabajador Seleccionado') !!}
                {!! Form::select('empleado[]', $empleadosDisponibles
                  ,  null
                  ,['class'=>'form-control selectAutocomplete empleado'
                  , 'title'=>'Seleccione Trabajador'] ) !!}
                  <small class="text-danger">{{$errors->first('empleado[]')}}</small>
              </div>

              <div class="form-group{{$errors->has('fecha')? ' has-error': ''}}">
                  {!! Form::label('fecha', 'Fecha de Inicio Progresivas') !!}
                  <div class="input-group date selectorSoloFecha" id="fecha_dp" data-target-input="nearest">
                    {!! Form::text('fecha',Carbon\Carbon::now()->format('d/m/Y')
                      ,  ['class'=>'fecha form-control datetimepicker-input', 'data-target'=>'#fecha_dp'] ) !!}
                    <div class="input-group-append" data-target="#fecha_dp" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                  <small class="text-danger">{{$errors->first('fecha')}}</small>
              </div>
              <div class="form-group{{$errors->has('anios_empresa')? ' has-error': ''}}">
                {!! Form::label('anios_empresa', 'Años trabajados en otra empresa') !!}
                {!! Form::number('anios_empresa'
                  ,  10
                  ,['class'=>'form-control'] ) !!}
                  <small class="text-danger">{{$errors->first('anios_empresa')}}</small>
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
  @endpush
