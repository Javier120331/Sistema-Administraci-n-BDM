@extends('master', [
  'menuActual' => 'registrarLicencia'])

  @section('contenido')
    <div class="mx-auto col-lg-6 col-md-8 col-sm-12">
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
          @if(session("resultado")->estado && session('resultado')->idDocumento!= -1)
            <a href='{{ action("MovimientosController@download"
              ,["id"=>session("resultado")->idDocumento])}}' class="btn btn-danger float-right" ><i class="far fa-file-pdf"></i> Descargar PDF</a>
        @endif
        </div>
      @endif
      <div class="card">
        <div class="card-header bg-suelditas text-white">
          <span>Registrar Licencia para Trabajador</span>
        </div>
        <div class="card-body">
          {!! Form::open(['method'=> 'POST'
                  , 'action'=>'LicenciasController@store'
                  , 'class' => 'form-horizontal']) !!}
              {!! csrf_field() !!}
              <div class="form-group{{$errors->has('empleado[]')? ' has-error': ''}}">
                {!! Form::label('empleado[]', 'Trabajador') !!}
                {!! Form::select('empleado[]', $empleadosDisponibles
                  ,  null
                  ,['class'=>'form-control selectAutocomplete empleado'
                  , 'title'=>'Seleccione Trabajador'] ) !!}
                  <small class="text-danger">{{$errors->first('empleado[]')}}</small>
              </div>
              <div class="form-group{{$errors->has('tipo')? ' has-error': ''}}">
                {!! Form::label('tipo', 'Tipo de Licencia') !!}
                {!! Form::select('tipo', $tiposLicencias
                  ,  null
                  ,['class'=>'form-control selectAutocomplete tipo'
                  , 'title'=>'Seleccione Tipo'] ) !!}
                  <small class="text-danger">{{$errors->first('tipo')}}</small>
              </div>
              <div class="form-group{{$errors->has('causa')? ' has-error': ''}}">
                {!! Form::label('causa', 'Causa') !!}
                {!! Form::select('causa', $causasLicencias
                  ,  null
                  ,['class'=>'form-control selectAutocomplete causa'
                  , 'title'=>'Seleccione Causa'] ) !!}
                  <small class="text-danger">{{$errors->first('causa')}}</small>
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

              <div class="div_cantidad form-group{{$errors->has('cantidad_dias')? ' has-error': ''}}">
                  {!! Form::label('cantidad_dias', 'Cantidad de Días') !!}
                  {!! Form::number("cantidad_dias", 1, ['class'=>'form-control cantidad_dias', 'min'=>'1']) !!}
                  <small class="text-danger">{{$errors->first('cantidad_dias')}}</small>
              </div>

              <div class=" form-group{{$errors->has('fecha_final')? ' has-error': ''}}">
                  {!! Form::label('fecha_final', 'Fecha Término') !!}
                  <div class="input-group date selectorSoloFechaVinculado2" id="fecha_fin_dp" data-target-input="nearest">

                  {!! Form::text("fecha_final", Carbon\Carbon::now()->format('d/m/Y'), ['class'=>'form-control fecha_final']) !!}
                  <div class="input-group-append" data-target="#fecha_fin_dp" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                  <small class="text-danger">{{$errors->first('fecha_final')}}</small>
              </div>
              <div class="form-group{{$errors->has('folio')? ' has-error': ''}}">
                  {!! Form::label('folio', 'Folio de Licencia') !!}
                  {!! Form::text("folio", null, ['class'=>'form-control only-numeros']) !!}
                  <small class="text-danger">{{$errors->first('folio')}}</small>
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
        <script type="text/javascript" src="{{asset('vendor/tinymce/tinymce.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('js/licencias.ingresar.js')}}"></script>

      @endpush
