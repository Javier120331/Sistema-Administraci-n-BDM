@extends('master', [
  'menuActual' => 'feriados'])

  @section('contenido')
    @include('templates/_cargando')

    <div class="modal fade" id="agregarFeriadoModal" tabindex="-1"
    role="dialog" aria-labelledby="agregarFeriadoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">Ingrese datos del día festivo</h5>
          </div>
          <div class="modal-body">
            <div class="row mensajes_agregar_feriado">

            </div>
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>Fecha</th><td id='feriado_agregar_fecha'></td>
                </tr>
                <tr>
                  <th>Descripción</th><td > <input type="text"
                    class="form-control"
                    id='feriado_agregar_descripcion' /></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary"
              data-dismiss="modal">Cancelar</button>
            <button type="button" id="agregar_feriado_btn" class="btn btn-primary"
              >Agregar Feriado</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="eliminarFeriadoModal" tabindex="-1"
    role="dialog" aria-labelledby="eliminarFeriadoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
             <h5 class="modal-title">¿Desea realmente eliminar el día festivo?</h5>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>Fecha</th><td id='feriado_eliminado_fecha'></td>
                </tr>
                <tr>
                  <th>Detalle</th><td id='feriado_eliminado_descripcion'></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary"
              data-dismiss="modal">Cancelar</button>
            <button type="button" id="eliminar_feriado_btn" class="btn btn-danger"
              data-dismiss="modal"
              >Eliminar</button>
          </div>
        </div>
      </div>
    </div>
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      <div class="row">
        <div class="mensaje_feriados mx-auto mb-2 col-lg-8 col-md-8 col-sm-12">
        </div>
      </div>
      <div class="row">
        <div class="mx-auto col-lg-8 col-md-8 col-sm-12">
          <div class="card">
            <div class="card-header bg-suelditas text-white">
              <span>Feriados en Línea</span>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-12 col-sm-12 col-md-12">

                  <div class="form-inline">
                    <div class="form-group mb-2">
                      {!! Form::label('anio', 'Seleccione Año') !!}
                      {!! Form::select('anio', $anios
                        ,  $anio
                        ,['class'=>'ml-3 form-control anio_select'
                        , 'title'=>'Seleccione Año a Importar'] ) !!}

                    </div>
                  </div>

                  <div class="form-group">
                    <button type="button"
                      class="btn btn-info"
                      id="importar_feriados_btn">Obtener Feriados del año en línea</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-2 mx-auto col-lg-8 col-md-12 col-sm-12">
          <div class="card">
            <div class="card-header">
              <span>Agregar Nuevo Festivo</span>
            </div>
            <div class="card-body">
              <div class="form-inline">
                <div class="form-group mb-2">
                  {!! Form::label('nuevo_feriado', 'Seleccione Fecha Festiva') !!}
                  <div class="input-group date agregar_feriados_date
                    selectorSoloFechaMoment ml-3" id="nuevo_feriado_dp" data-target-input="nearest">
                    {!! Form::text('nuevo_feriado',null
                      ,  ['class'=>'fecha nuevo_feriado form-control datetimepicker-input', 'data-target'=>'#nuevo_feriado_dp'] ) !!}
                      <div class="input-group-append" data-target="#nuevo_feriado_dp" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <button type="button"
                  class="btn btn-primary"
                  id="mostrar_agregar_feriados_btn">Agregar Feriado</button>
                </div>
            </div>
          </div>

        </div>
      </div>

      <div class="row mt-5">
        <div class="mx-auto col-lg-8 col-md-8 col-sm-12">
          <div class="card">
            <div class="card-header bg-suelditas text-white">
              <span>Feriados definidos para el año {{$anio}}</span>
            </div>
            <div class="card-body">
              <div id="feriados_calendar">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @stop
  @push("css")
        {!! Html::style(asset('vendor/fullcalendar/fullcalendar.min.css')) !!}
  @endpush
  @push('javascript')
        <script src="{!! asset('vendor/fullcalendar/fullcalendar.min.js') !!}"></script>
        <script src="{!! asset('vendor/fullcalendar/locale/es.js') !!}"></script>
        <script src="{!! asset('js/feriados.index.js') !!}"></script>
  @endpush
