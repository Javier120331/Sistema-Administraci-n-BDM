@extends('master', [
  'menuActual' => 'liquidaciones'])

  @section('contenido')
    <div class="col-lg-9 col-md-12 col-sm-12 mx-auto">
      <div class="card">
          <div class="card-header bg-suelditas text-white">Generar Liquidaciones</div>
          <div class="card-body text-center">
            <div class="page-header">
                <h4 class="text-center">Permite generar las liquidaciones
                  de sueldo para los Cosechadores
                </h4>
            </div>
              @if(session()->has('exito'))
                <p class="bg-info infoGeneral text-center">
                  {{ session()->get('exito')}}
                </p>
              @endif
              @if(session()->has('mensaje'))
                <p class="bg-primary mensajeGeneral text-center">
                  {{session()->get('mensaje')}}
                </p>
              @endif
              @if(session()->has('error'))
                <p class="bg-danger errorGeneral text-center">
                  {{session()->get('error')}}
                </p>
              @endif
              <div class="row">
                <div class="mx-auto col-md-7 col-lg-7 col-sm-12">
                  {!! Form::open(['method'=> 'POST'
                      , 'action'=>'LiquidacionController@post'
                      , 'class' => 'form-horizontal']) !!}
                      {!! csrf_field() !!}
                <fieldset>
                  <div class="form-group{{$errors->has('seleccionarTodos')? ' has-error': ''}}">
                    {!! Form::label('seleccionarTodos', 'Seleccionar Todos los Cosechadores') !!}
                    {!! Form::checkbox('seleccionarTodos'
                      ,"todos", true, ["class"=>"todosCheckBox"]) !!}
                      <small class="text-danger">{{$errors->first('seleccionarTodos')}}</small>
                  </div>
                  <div class="form-group{{$errors->has('grupo')? ' has-error': ''}}">
                    {!! Form::label('grupo', 'Grupo') !!}
                    {!! Form::select('grupo', $gruposDisponibles
                      , old('grupo')
                      ,['class'=>'form-control selectAutocomplete grupo'
                      , 'title'=>'Seleccione Grupo'
                      , "disabled" => true] ) !!}
                      <small class="text-danger">{{$errors->first('grupo')}}</small>
                    </div>
                    <div class="form-group{{$errors->has('seleccionarTodosPorGrupo')? ' has-error': ''}}">
                      {!! Form::label('seleccionarTodosPorGrupo', 'Seleccionar Todos los Cosechadores del Grupo') !!}
                      {!! Form::checkbox('seleccionarTodosPorGrupo'
                        ,"todos", true, ["class"=>"todosCheckBoxGrupo"]) !!}
                        <small class="text-danger">{{$errors->first('seleccionarTodosPorGrupo')}}</small>
                    </div>
                  <div class="form-group{{$errors->has('cosechador[]')? ' has-error': ''}}">
                    {!! Form::label('cosechador[]', 'Cosechador') !!}
                    {!! Form::select('cosechador[]', $trabajadoresDisponibles
                      ,  $valorTrabajadores->keys()->toArray()
                      ,['class'=>'form-control selectAutocomplete cosechador'
                       , "multiple"=> true
                      , 'title'=>'Seleccione Cosechador'
                      , "disabled"=>true] ) !!}
                      <small class="text-danger">{{$errors->first('cosechador[]')}}</small>
                    </div>
                    <div class="form-group{{$errors->has('mes')? ' has-error': ''}}">
                        {!! Form::label('mes', 'Mes') !!}
                        <div class="input-group date selectorSoloMes" id="mes_dp" data-target-input="nearest">
                          {!! Form::text('mes',Carbon\Carbon::now()->format('d/m/Y')
                            ,  ['class'=>'mes form-control datetimepicker-input', 'data-target'=>'#mes_dp'] ) !!}
                          <div class="input-group-append" data-target="#mes_dp" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                          </div>
                        </div>
                        <small class="text-danger">{{$errors->first('mes')}}</small>
                    </div>
                      <div class="btn-group pull-right">
                          {!! Form::submit('Generar',['class'=>'btn btn-success']) !!}
                        </div>
                    </fieldset>
                    {!! Form::close()!!}
                </div>
          </div>
     </div>
    </div>

  @stop
  @push('javascript')
    <script type="text/javascript" src="{{asset('js/liquidaciones.index.js')}}"></script>
  @endpush
