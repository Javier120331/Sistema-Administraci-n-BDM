@extends('master', [
  'menuActual' => 'ingresarVacaciones'])

  @section('contenido')
    <div class="modal fade" id="modal-adv-vac" tabindex="-1" role="dialog" aria-labelledby="modal-adv-vac" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Desea realmente registrar vacaciones?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body mensaje-adv-vac">

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
            <button type="button" id="modal-adv-aceptar" class="btn btn-danger">Registrar de todos modos</button>
          </div>
        </div>
      </div>
    </div>
    <div class="mx-auto col-lg-6 col-md-8 col-sm-12">
      <div class="alert alert-danger d-none advertencia-vacaciones">
        <span></span>
      </div>
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
          @if(session("resultado")->estado)
            <a href='{{ action("VacacionesController@download"
              ,["id"=>session("resultado")->vacacion->id])}}' target="_blank" class="btn btn-danger float-right" ><i class="far fa-file-pdf"></i> Descargar PDF</a>
        @endif
        </div>
      @endif
      <div class="card">
        <div class="card-header bg-suelditas text-white">
          <span>Crear Vacaciones para Trabajador</span>
        </div>
        <div class="card-body">
          {!! Form::open(['method'=> 'POST'
                  , 'action'=>'VacacionesController@store'
                  , 'class' => 'form-horizontal form-vacaciones']) !!}
              {!! csrf_field() !!}
              <div class="form-group{{$errors->has('empleado[]')? ' has-error': ''}}">
                {!! Form::label('empleado[]', 'Trabajador Seleccionado') !!}
                {!! Form::select('empleado[]', $empleadosDisponibles
                  ,  null
                  ,['class'=>'form-control selectAutocomplete empleado'
                  , 'title'=>'Seleccione Trabajador'] ) !!}
                  <small class="text-danger">{{$errors->first('empleado[]')}}</small>
              </div>
              <div class="form-group{{$errors->has('periodo[]')? ' has-error': ''}}">
                {!! Form::label('periodo[]', 'Periodo Seleccionado') !!}
                {!! Form::select('periodo[]', []
                  ,  null
                  ,['class'=>'form-control selectAutocomplete periodo'
                  , 'title'=>'Seleccione Periodo'] ) !!}
                  <small class="text-danger">{{$errors->first('periodo[]')}}</small>
              </div>
              <div class="form-group{{$errors->has('fecha_inicio')? ' has-error': ''}}">
                  {!! Form::label('fecha_inicio', 'Fecha de Inicio') !!}
                  <div class="input-group date selectorSoloFecha" id="fecha_dp" data-target-input="nearest">
                    {!! Form::text('fecha_inicio',Carbon\Carbon::now()->format('d/m/Y')
                      ,  ['class'=>'fecha form-control datetimepicker-input fecha_inicio', 'data-target'=>'#fecha_dp'] ) !!}
                    <div class="input-group-append" data-target="#fecha_dp" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                  <small class="text-danger">{{$errors->first('fecha_inicio')}}</small>
              </div>
              <div class="form-group dias_disponibles">
                <label class="font-weight-bold">Días disponibles</label>
                <span class="font-weight-bold"></span>
              </div>
              <div class="form-group{{$errors->has('dias')? ' has-error': ''}}">
                {!! Form::label('cantidad_dias', 'Cantidad de Días') !!}
                {!! Form::number('cantidad_dias'
                  ,  15
                  ,['class'=>'form-control cantidad_dias'
                    , 'min'=>"1" ] ) !!}
                  <small class="text-danger">{{$errors->first('dias')}}</small>
                </div>
                <div class="form-group{{$errors->has('fecha_termino')? ' has-error': ''}}">
                  {!! Form::label('fecha_termino', 'Fecha de Termino') !!}
                  <div class='input-group date'>
                    {!! Form::text('fecha_termino',null
                        , ['class'=>'form-control fecha_final', 'readonly' => 'true'] ) !!}
                  </div>
                  <small class="text-danger">{{$errors->first('fecha_termino')}}</small>
              </div>
              <div class="btn-group">
                  {!! Form::button('Ingresar',['class'=>'btn btn-success ingresar-vacaciones-btn']) !!}
              </div>
          {!! Form::close()!!}
        </div>
      </div>
    </div>
  @stop
  @push('javascript')
    <script type="text/javascript" src="{{asset('js/vacaciones.ingresar.js')}}"></script>

  @endpush
