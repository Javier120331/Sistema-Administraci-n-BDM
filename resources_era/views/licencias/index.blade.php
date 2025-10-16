@extends('master', [
  'menuActual' => 'licencias'])

  @section('contenido')
    @include("templates/_modal_confirmacion", ['accion'=>'Anular'
      , 'entidad'=> 'Licencia'])
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      @include("templates/_mensajes_infos")
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
        </div>
      @endif
      <div class="row">
        <div class="mx-auto col-lg-8 col-md-6 col-sm-12">
          <div class="card">
            <div class="card-header">
              <span>Filtrar</span>
            </div>
            <div class="card-body pl-5">
              <div class="form-inline">


              <div class="form-group{{$errors->has('estado')? ' has-error': ''}}">
                {!! Form::label('estado', 'Estado Seleccionado', ['class'=> 'mr-2']) !!}
                {!! Form::select('estado', array(1=>'En Curso',0=>'Finalizada')
                  ,  null
                  ,['class'=>'form-control selectAutocompleteNoDefault filtroEstado'
                  , 'title'=>'Seleccione Estado'] ) !!}
                  <small class="text-danger">{{$errors->first('estado')}}</small>

              </div>
              <div class="ml-2 form-group{{$errors->has('causa')? ' has-error': ''}}">
                {!! Form::label('causa', 'Causa de Licencia', ['class'=> 'mr-2']) !!}
                {!! Form::select('causa', $causasLicencias
                  ,  null
                  ,['class'=>'ml-2 form-control  form-control-lg selectAutocompleteConDefault causaSelect'
                  , 'title'=>'Seleccione Causa'] ) !!}
                  <small class="text-danger">{{$errors->first('causa')}}</small>
              </div>
              <div class="ml-2 form-group{{$errors->has('tipo')? ' has-error': ''}}">
                {!! Form::label('tipo', 'Tipo de Licencia', ['class'=> 'mr-2']) !!}
                {!! Form::select('tipo', $tiposLicencias
                  ,  null
                  ,['class'=>'ml-2 form-control form-control-lg selectAutocompleteConDefault tipoSelect'
                  , 'title'=>'Seleccione Tipo'] ) !!}
                  <small class="text-danger">{{$errors->first('tipo')}}</small>
              </div>
              <div class="mt-2 ml-4 btn-group">
                <button type="button" class="btn btn-info"  id="btn_fintro_licencia">Filtrar</button>
              </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-5">

        <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
          {!! Html::link(action("LicenciasController@create")
              , "Registrar Licencia",["class"=>'btn btn-primary'])  !!}
          <div class="card">
            <div class="card-header bg-suelditas text-white">Licencias en Curso</div>
            <div class="card-body">
              <table class="table table-striped table-bordered dataTable no-footer" id="licencias-table">
                <thead>
                  <tr>
                    <th>Rut de Trabajador</th>
                    <th>Nombre de Trabajador</th>
                    <th>Causa</th>
                    <th>Tipo de Licencia</th>
                    <th>Fecha de Inicio</th>
                    <th>Cantidad de días</th>
                    <th>Fecha de Termino </th>
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
    <script type="text/javascript" src="{{asset('js/licencias.index.js')}}"></script>
  @endpush
