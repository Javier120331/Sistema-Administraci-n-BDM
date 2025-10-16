@extends('master', [
  'menuActual' => 'ingresarFiniquito'])

  @section('contenido')
    @include("templates/_modal_confirmacion", ['accion'=>'Finiquitar'
    , 'entidad'=> 'Personal'])
    <div class="mx-auto col-lg-12 col-md-12 col-sm-12">
      @include("templates/_mensajes_infos")
      @if(session()->has('resultado'))
        <div class="alert alert-{{session('resultado')->estado? 'primary': 'danger'}}">
          {{session('resultado')->mensaje}}
        </div>
      @endif
      @if(isset($mensaje))
        <div class="alert alert-success">
          <span>{{$mensaje}}</span>
          @if(isset($idFiniquito))
            <a href='{{ action("FiniquitosController@download"
              ,["id"=>$idFiniquito])}}' target="_blank" class="btn btn-danger float-right" ><i class="far fa-file-pdf"></i> Descargar PDF</a>
            @endif

          </div>
        @endif
        <div class="row">
          <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card">
              <div class="card-header bg-suelditas text-white">Generar Finiquito</div>
              <div class="card-body">
                {!! Form::open(['method'=> 'POST'
                  , 'action'=>'FiniquitosController@cargarDatosFiniquito'
                  , 'class' => 'form-horizontal'
                  ]) !!}
                  {!! csrf_field() !!}
                  <div class="form-group{{$errors->has('empleado[]')? ' has-error': ''}}">
                    {!! Form::label('empleado[]', 'Trabajador Seleccionado') !!}
                    {!! Form::select('empleado[]', $empleadosDisponibles
                      ,  isset($empSel)? $empSel->id:old('empleado[]')
                      ,['class'=>'form-control selectAutocomplete empleado'
                      , 'title'=>'Seleccione Trabajador'] ) !!}
                      <small class="text-danger">{{$errors->first('empleado[]')}}</small>
                    </div>
                    <div class="btn-group">
                      {!! Form::submit('Cargar Datos',['class'=>'btn btn-success']) !!}
                    </div>
                    {!! Form::close()!!}

                  </div>
                </div>
              </div>
              <div class="col-lg col-md col-sm">
                @if(isset($empSel))
                  <div class="card cardDetalleFiniquito">
                    <div class="card-header bg-suelditas text-white">Detalle de Finiquito</div>
                    <div class="card-body">
                      {!! Form::open(['method'=> 'POST'
                        , 'action'=>['FiniquitosController@finiquitar'
                        , session()->has('empSel')? session('empSel')->id: $empSel->id]
                        , 'class' => 'form-horizontal'
                        , 'id'=>'form-finiquito']) !!}
                        {!! csrf_field() !!}
                        <div class="form-group row">
                          <label for="nombreEmpTxt"
                          class="col-sm-4 col-form-label">Nombre</label>
                          <div class="col-sm-8">
                            <input type="text" readonly
                            class="form-control-plaintext"
                            id="nombreEmpTxt" value="{{$empSel->nombre}}">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="rutEmpTxt"
                          class="col-sm-4 col-form-label">RUT</label>
                          <div class="col-sm-8">
                            <input type="text" readonly
                            class="form-control-plaintext"
                            id="rutEmpTxt" value="{{$empSel->rut}}">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="fechaCEmpTxt"
                          class="col-sm-4 col-form-label">Fecha de Contrato</label>
                          <div class="col-sm-8">
                            <input type="text" readonly
                            class="form-control-plaintext"
                            id="fechaCEmpTxt" value="{{$empSel->fecha_inicio_contrato->toDateString()}}">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="cargoEmpTxt"
                          class="col-sm-4 col-form-label">Cargo</label>
                          <div class="col-sm-8">
                            <input type="text" readonly
                            class="form-control-plaintext"
                            id="cargoEmpTxt" value="{{$empSel
                              ->getCargoActual()->first()->cargo()->first()->nombre}}">
                            </div>
                          </div>
                          <div class="form-group row">
                            <label for="ciudadEmpTxt"
                            class="col-sm-4 col-form-label">Ciudad</label>
                            <div class="col-sm-8">
                              <input type="text" readonly
                              class="form-control-plaintext"
                              id="ciudadEmpTxt" value="{{$empSel
                                ->comuna()->first()->nombre}}">
                              </div>
                            </div>
                            <div class="row form-group{{$errors->has('fecha_finiq')? ' has-error': ''}}">
                              {!! Form::label('fecha_finiq', 'Fecha de Finiquito'
                                , ['class'=>'col-sm-4 col-form-label']) !!}
                                <div class="col-sm-8 input-group date selectorSoloFecha" id="fecha_finiq_dp" data-target-input="nearest">
                                  {!! Form::text('fecha_finiq',Carbon\Carbon::now()->format('d/m/Y')
                                    ,  ['class'=>'fecha_finiq form-control datetimepicker-input'
                                    , 'data-target'=>'#fecha_finiq_dp'] ) !!}
                                    <div class="input-group-append" data-target="#fecha_finiq_dp" data-toggle="datetimepicker">
                                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                  </div>
                                  <small class="text-danger">{{$errors->first('fecha_finiq')}}</small>
                                </div>
                                <div class="form-group{{$errors->has('causaFiniquito[]')? ' has-error': ''}} row">
                                  {!! Form::label('causaFiniquito[]', 'Causa', ['class'=>'col-sm-4 col-form-label']) !!}
                                  <div class="col-sm-8 input-group " >

                                    {!! Form::select('causaFiniquito[]', $causasDisponibles
                                      ,  old('causaFiniquito[]')
                                      ,['class'=>'form-control selectAutocomplete causaFiniquito'
                                      , 'title'=>'Seleccione Causa de Finiquito'] ) !!}
                                      <small class="text-danger">{{$errors->first('causaFiniquito[]')}}</small>
                                    </div>
                                  </div>
                                  @if(isset($sueldosFiniquito) )
                                    @if(count($sueldosFiniquito) > 0)
                                      <div class="form-group">

                                        <table class="table table-hovered">
                                          <thead >
                                            <tr>
                                              <th>MES</th>
                                              <th>AÑO</th>
                                              <th>Dias Trabajados</th>
                                              <th>Total Ganado</th>
                                              <th>Imponible</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            @foreach($sueldosFiniquito as $sueldo)
                                              <tr>
                                                <td>{{ strtoupper($sueldo->fecha_carbon->monthName)}}</td>
                                                <td>{{$sueldo->fecha_carbon->year}}</td>
                                                <td>30</td>
                                                <td>{{ $sueldo->sueldo_chile}}</td>
                                                <td>{{$sueldo->sueldo_chile}}</td>
                                              </tr>
                                            @endforeach
                                          </tbody>
                                        </table>
                                      </div>
                                      <div class="form-group row">
                                        <label for="totalEmpTxt"
                                        class="col-sm-4 col-form-label">Suma de Totales Ganados</label>
                                        <div class="col-sm-8">
                                          <input type="text" readonly
                                          class="form-control-plaintext"
                                          id="totalEmpTxt" value="{{$resumen->total_ganado}}">
                                        </div>
                                      </div>
                                    @else
                                      <div class="form-group row">
                                        <div class="alert  alert-warning col-sm-12 text-center">
                                          <div class="row" >
                                            <p class="mx-auto col-sm-8">Los registros presentan licencias o vacaciones</p>
                                          </div>
                                          <div class="row" >
                                            <p class="mx-auto col-sm-8">Debe ingresar manualmente la remuneración promedio</p>
                                          </div>
                                        </div>
                                      </div>

                                    @endif

                                    <div class="form-group {{$errors->has('promedio_sueldo')? ' has-error': ''}}  row">
                                      <label for="prom_rem_Txt"
                                      class="col-sm-4 col-form-label">Remuneración Promedio</label>

                                      <div class="col-sm-8">
                                        <div class="input-group mb-3">
                                          <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                          </div>
                                          {!! Form::text('promedio_sueldo',!$errors->has('promedio_sueldo') && isset($resumen->promedio) ? $resumen->promedio : ''
                                            ,  ['class'=>'form-control input-moneda input-finiquito'
                                            , 'id'=>'prom_rem_txt'] ) !!}

                                          </div>
                                          <small class="text-danger">{{$errors->first('promedio_sueldo')}}</small>

                                        </div>
                                      </div>
                                      <div class="form-group row">
                                        <label for="anios_servicio_total_Txt"
                                        class="col-sm-4 col-form-label" >Años de Servicio</label>
                                        <div class="col-sm-8">
                                          {!! Form::text('anios_servicio',$datosServicio->aniosServicio >11 ? 11: $datosServicio->aniosServicio
                                            ,  ['class'=>'form-control-plaintext'
                                            , 'readonly'=>true
                                            , 'id'=>'anios_servicio_total_Txt'] ) !!}
                                          </div>
                                        </div>
                                        <div class="form-group {{$errors->has('anios_servicio_total')? ' has-error': ''}}  row">
                                          <label for="total_pagar_anios_servicio_txt"
                                          class="col-sm-4 col-form-label" >Total a Pagar por Años de Servicio</label>
                                          <div class="col-sm-8">
                                            <div class="input-group mb-3">
                                              <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                              </div>
                                              {!! Form::text('anios_servicio_total'
                                                , !$errors->has('anios_servicio_total') && isset($datosServicio->totalPagarServicio)
                                                  && isset($causaSeleccionada) && $causaSeleccionada->paga_anios == 1 ? $datosServicio->totalPagarServicio : '0'
                                                ,  ['class'=>'form-control input-moneda input-finiquito'
                                                , 'id'=>'total_pagar_anios_servicio_txt'] ) !!}
                                              </div>
                                              <small class="text-danger">{{$errors->first('anios_servicio_total')}}</small>

                                            </div>
                                          </div>
                                          <div class="form-group row ">
                                            <div class=" col-sm-4 col-form-label">
                                              <label for="pagar_mes_aviso_chk mr-2">Pagar mes de aviso</label>

                                              {!! Form::checkbox('pagar_mes_aviso_chk', $empSel->id,null
                                                , ['data-toggle'=>"toggle"
                                                , 'data-on'=>"Sí"
                                                , 'data-off'=> "No"
                                                , 'data-size'=>'sm'

                                                ,'id'=>'pagar_mes_aviso_chk' ]) !!}

                                              </div>
                                              <div class="col-sm-8 d-none pagar_mes_aviso_div">
                                                <div class="input-group mb-3">
                                                  <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                  </div>
                                                  {!! Form::text('mes_aviso_valor',null
                                                    ,  ['class'=>'form-control input-moneda input-finiquito', 'id'=> 'mes_aviso_valor_txt'] ) !!}
                                                    </div
                                                  </div>
                                                </div>
                                              </div>
                                            @endif
                                            <div class="form-group {{$errors->has('dias_vacaciones_txt')? ' has-error': ''}}  row">
                                              <label for="seguro_desempleo_txt"
                                              class="col-sm-4 col-form-label" >Dias hábiles de vacaciones</label>
                                              <div class="col-sm-8">
                                                <div class="input-group mb-3">
                                                  {!! Form::text('dias_vacaciones_txt'
                                                    ,!$errors->has('dias_vacaciones_txt')?$resumen->diasHabilesVac:''
                                                    ,  ['class'=>'form-control input-finiquito'
                                                    , 'id'=>'dias_vacaciones_txt'] ) !!}


                                                  </div>
                                                  <small class="text-danger">{{$errors->first('dias_vacaciones_txt')}}</small>
                                                </div>
                                              </div>
                                              <div class="form-group {{$errors->has('total_dias_vacaciones_txt')? ' has-error': ''}} row">
                                                <label for="total_dias_vacaciones_txt"
                                                class="col-sm-4 col-form-label" >Total por Dias hábiles de vacaciones</label>
                                                <div class="col-sm-8">
                                                  <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                      <span class="input-group-text">$</span>
                                                    </div>
                                                    {!! Form::text('total_dias_vacaciones_txt'
                                                      ,!$errors->has('total_dias_vacaciones_txt')?App\Utilidades\NumerosUtils::getMoneda($resumen->valorVacaciones):''
                                                      ,  ['class'=>'form-control input-moneda input-finiquito'
                                                      , 'id'=>'total_dias_vacaciones_txt'] ) !!}

                                                    </div>
                                                    <small class="text-danger">{{$errors->first('total_dias_vacaciones_txt')}}</small>
                                                  </div>
                                                </div>
                                                <div class="form-group {{$errors->has('dias_inhabiles_vacaciones_txt')? ' has-error': ''}}  row">
                                                  <label for="seguro_desempleo_txt"
                                                  class="col-sm-4 col-form-label" >Dias inhábiles de vacaciones</label>
                                                  <div class="col-sm-8">
                                                    <div class="input-group mb-3">
                                                      {!! Form::text('dias_inhabiles_vacaciones_txt'
                                                        ,!$errors->has('dias_inhabiles_vacaciones_txt')?$resumen->diasInhabilesVac:''
                                                        ,  ['class'=>'form-control input-finiquito'
                                                        , 'id'=>'dias_inhabiles_vacaciones_txt'] ) !!}


                                                      </div>
                                                      <small class="text-danger">{{$errors->first('dias_inhabiles_vacaciones_txt')}}</small>
                                                    </div>
                                                  </div>
                                                  <div class="form-group {{$errors->has('total_dias_inhabiles_vacaciones_txt')? ' has-error': ''}} row">
                                                    <label for="total_dias_vacaciones_txt"
                                                    class="col-sm-4 col-form-label" >Total por Dias inhábiles de vacaciones</label>
                                                    <div class="col-sm-8">
                                                      <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                          <span class="input-group-text">$</span>
                                                        </div>
                                                        {!! Form::text('total_dias_inhabiles_vacaciones_txt'
                                                          ,!$errors->has('total_dias_inhabiles_vacaciones_txt')?App\Utilidades\NumerosUtils::getMoneda($resumen->totalInhabilesVacaciones):''
                                                          ,  ['class'=>'form-control input-moneda input-finiquito'
                                                          , 'id'=>'total_dias_inhabiles_vacaciones_txt'] ) !!}

                                                        </div>
                                                        <small class="text-danger">{{$errors->first('total_dias_inhabiles_vacaciones_txt')}}</small>
                                                      </div>
                                                    </div>
                                                    <div class="form-group row">
                                                      <label for="seguro_desempleo_txt"
                                                      class="col-sm-4 col-form-label" >Seguro de Desempleo</label>
                                                      <div class="col-sm-8">
                                                        <div class="input-group mb-3">
                                                          <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                          </div>
                                                          {!! Form::text('seguro_desempleo_txt',0
                                                            ,  ['class'=>'form-control input-moneda input-finiquito'
                                                            , 'id'=>'seguro_desempleo_txt'] ) !!}
                                                          </div>
                                                        </div>
                                                      </div>
                                                      <div class="form-group row">
                                                        <label for="prestamo_empresa_txt"
                                                        class="col-sm-4 col-form-label" >Prestamo de Empresa</label>
                                                        <div class="col-sm-8">
                                                          <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                              <span class="input-group-text">$</span>
                                                            </div>
                                                            {!! Form::text('prestamo_empresa_txt',0
                                                              ,  ['class'=>'form-control input-moneda input-finiquito'
                                                              , 'id'=>'prestamo_empresa_txt'] ) !!}
                                                            </div>
                                                          </div>
                                                        </div>

                                                        <div class="form-group row">
                                                          <label for="descuento_sobregiro_txt"
                                                          class="col-sm-4 col-form-label" >Descuento Sobregiro</label>
                                                          <div class="col-sm-8">
                                                            <div class="input-group mb-3">
                                                              <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                              </div>
                                                              {!! Form::text('descuento_sobregiro_txt',0
                                                                ,  ['class'=>'form-control input-moneda input-finiquito'
                                                                , 'id'=>'descuento_sobregiro_txt'] ) !!}
                                                              </div>
                                                            </div>
                                                          </div>
                                                          <div class="form-group row">
                                                            <label for="descuento_convenios_txt"
                                                            class="col-sm-4 col-form-label" >Descuento Convenios</label>
                                                            <div class="col-sm-8">
                                                              <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                  <span class="input-group-text">$</span>
                                                                </div>
                                                                {!! Form::text('descuento_convenios_txt',0
                                                                  ,  ['class'=>'form-control input-moneda input-finiquito'
                                                                  , 'id'=>'descuento_convenios_txt'] ) !!}
                                                                </div>
                                                              </div>
                                                            </div>
                                                        <div class="form-group row">
                                                          <label for="total_pagar_finiquito_txt"
                                                          class="col-sm-4 col-form-label" >Total a Pagar</label>
                                                          <div class="col-sm-8">
                                                            <div class="input-group  mb-3">
                                                              <div class="input-group-prepend">
                                                                <span class="input-group-text bg-transparent border border-white">$</span>
                                                              </div>
                                                              {!! Form::text('total_pagar_finiquito_txt'
                                                                ,null
                                                                ,  ['class'=>'form-control-plaintext'
                                                                , 'readonly'=>true
                                                                , 'id'=>'total_pagar_finiquito_txt'] ) !!}
                                                              </div>
                                                            </div>
                                                          </div>
                                                          <div class="btn-group">
                                                            {!! Form::button('Finiquitar',['class'=>'btn btn-danger', 'id'=>'finiquitar_btn']) !!}
                                                          </div>
                                                          {!! Form::close()!!}
                                                        </div>
                                                      @endif
                                                    </div>
                                                  </div>
                                                </div>
                                              @stop
                                              @push('javascript')
                                                <script src="{!! asset('js/finiquitos.create.js') !!}"></script>
                                              @endpush
