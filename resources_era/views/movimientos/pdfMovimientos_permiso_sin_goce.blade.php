<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Acuerdo de Permiso de ">
    <meta name="author" content="Bosques del Mauco">
    <title>Inversiones Bosques del Mauco -  {{$movimiento->titulo_documento}}</title>
    <style type="text/css">
      .fila{
        width:100%;
        overflow:auto;
      }
      .div_logo{
        float:left;
        width:20%;
      }
      .div_logo > img{
            height:80px;
      }
      .div_area{
        float:right;
        width:20%;
      }

      .centrado_texto{
        text-align:justify;
      }
      .parrafo{
        text-align: justify;
        font-size:14px;
        margin-top: 100px;
      }
      .lista_detalle > li{
        margin-top: 10px;
        font-size:14px;
      }
      .lista_detalle{
        margin-top: 50px;
      }
      .letra_14{
        font-size: 14px;
      }
      .bloque_firmas{
        margin-top:200px;
      }
      .bloque_rut{
        margin-top:50px;
      }
      .bloque_firmas, .bloque_rut{
        font-size:16px;
      }
      .bloque_firmas  .base_firma{
        width: 200px;
        display:inline-block;
        border-bottom: 1px;
        border-style:solid;
        border-color: black;

      }
    </style>
</head>
<body>

  <div class="fila">
      <div class="div_logo">
          {!! Html::image('img/logo.png') !!}
      </div>
      <div class="div_area">
        <h3>{{$movimiento->area}}</h3>
      </div>
  </div>
  <div class="fila centrado_texto">
    <h3>INVERSIONES BOSQUES DEL MAUCO S.A.</h3>
    <h5>{{$movimiento->titulo_documento}}</h5>
  </div>
  <div class="fila parrafo">
    <p>En Quintero a {{$movimiento->fecha_documento->format('j F \\d\\e\\l Y')}}</p>
    <br/>
    <p>  Entre Inv. Bosques del Mauco S.A.,representada por la Sra {{$movimiento->nombre_encargado}},
       en su calidad de jefe de personal, y don {{$movimiento->nombre_empleado}}	C.I. {{$movimiento->rut_empleado}}
        quienes en adelante se denominarán “el empleador” y el “trabajador” respectivamente, se ha convenido lo siguiente:
    </p>
    <p >
      <ol class="lista_detalle">
        @if($movimiento->fecha_termino_movimientos != null )
          <li>El trabajador ha solicitado al empleador un permiso sin goce de
            remuneraciones desde el día  {{$movimiento->fecha_inicio_movimientos->format('j F \\d\\e\\l Y')}}
            hasta el día {{$movimiento->fecha_termino_movimientos->format('j F \\d\\e\\l Y')}}.</li>
        @else
          <li>El trabajador ha solicitado al empleador sin permiso con goce de
            remuneraciones para el día {{$movimiento->fecha_inicio_movimientos->format('j F \\d\\e\\l Y')}}</li>
        @endif
        <li>El empleador acepta conceder el permiso solicitado comprometiéndose el trabajador, a su vez, a reintegrarse a sus labores a más tardar el siguiente día de aquel fijado como día de término del referido permiso.</li>
        <li>3.	Las partes acuerdan y determinan expresamente dejar totalmente suspendida en forma plena y absoluta la relación laboral que une a las partes durante el período  correspondiente  al  permiso sin goce de sueldo,  el  cual  no  constituirá tiempo trabajado para ningún efecto legal o convencional.</li>
        <li>4.	Se deja, asimismo expresamente establecido que el trabajador queda liberado de ejecutar cotizaciones previsionales por el período correspondiente al permiso sin goce de sueldo, liberación esencial y determinante para el otorgamiento del permiso del presente acuerdo, lo cual el trabajador acepta.</li>
        <li>5.	El trabajador se compromete por su parte a dar cumplimiento directamente  y por sí mismo al pago de la cotización previsional y de la cotización de salud que le correspondiese de acuerdo al contrato con las instituciones previsionales y de saludo a las que se encuentre afiliado, liberando al empleador de toda responsabilidad  al respecto, por el período del permiso sin goce de remuneración.</li>
      </ol>
    </p>
    <p>
      Previa lectura las partes Ratifican y Firman
    </p>
  </div>
  <div class="fila bloque_firmas letra_16">
    <div >
      <span>FIRMA</span><span class="base_firma"></span>
      <span>EMPLEADOR</span><span class="base_firma"></span>
    </div>
    <div class="fila bloque_rut letra_16">
      <span>RUT</span>&nbsp;<span>{{$movimiento->rut_empleado}}</span>
    </div>
  </div>
</body>
</html>
