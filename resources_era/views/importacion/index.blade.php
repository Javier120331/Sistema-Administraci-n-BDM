@extends('master', [
  'menuActual' => 'importacion'])

  @section('contenido')

    <div class="col-lg-9 col-md-12 col-sm-12 mx-auto">
      @if(session()->has('mensaje'))
        <div class="text-info text-center row infoGeneral text-center">
          <h3 class="text-center">{{ session()->get("mensaje") }}</h3>
        </div>
      @endif
      <div class="card">
          <div class="card-header bg-suelditas text-white">Importar Movimientos</div>
          <div class="card-body text-center">
            <div class="page-header">
                <h5 class="text-center">Permite importar datos desde
                  deFontana para efectuar el proceso de Liquidación
                </h5>
            </div>
            <div class="mensajesResultado hidden">
                <p class="bg-info text-white infoGeneral text-center">
                </p>

                <p class="bg-danger text-white errorGeneral text-center">
                </p>
            </div>
            {!! csrf_field() !!}
            {{ Html::link(url('#')
            , 'Importar Información de Cosechadores'
            , ['class'=>'btn btn-success btnSubirLiquidacion'])}}
          </div>
     </div>
     <div class="card mt-3">
       <div class="card-header bg-suelditas text-white">Importar movimientos de personal</div>
       <div class="card-body text-center">
         <div class="page-header">
             <h5 class="text-center">Permite importar datos desde
               deFontana para efectuar la generación de finiquitos
             </h5>
         </div>
         <div class="mensajesResultado hidden">
             <p class="bg-info text-white infoGeneral text-center">
             </p>

             <p class="bg-danger text-white errorGeneral text-center">
             </p>
         </div>
         {!! csrf_field() !!}
         {{ Html::link(url('#')
         , 'Importar Información de Movimientos'
         , ['class'=>'btn btn-success btnSubirRemuneraciones'])}}
       </div>
     </div>
     <div class="rollback_importacion d-none">
       <div class="card mt-3">
         <div class="card-header text-white bg-suelditas">Efectuar Rollback</div>
         <div class="card-body text-center">
           {!! Form::open(['method'=> 'POST'
               , 'action'=>'ImportacionController@efectuarRollbackLiquidacion'
               , 'class' => 'form-horizontal']) !!}
               {!! csrf_field() !!}
               <div class="page-header">
                 <h4 class="text-center">Al efectuar el Rollback de la producción se efectúa
                   la eliminación de todos los datos de movimientos entre el día 21 del mes
                   anterior hasta el 20 del mes siguiente
                 </h4>
               </div>

               {!! Form::submit('Efectuar Rollback',['class'=>'btn btn-danger']) !!}
               {!! Form::close()!!}
             </div>
           </div>
     </div>
    </div>

    <div class="modal fade" id="modalImportando"
      tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Importando</h4>
          </div>
          <div class="modal-body">
            <h4>Importando Datos</h4>
            <div class="progress progress-striped active">
              <div class="progress-bar" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                style="width: 100%">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @stop
  @push('javascript')
    <script type="text/javascript" src="{{asset('js/importaciones.index.js')}}"></script>
  @endpush
