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
    margin-top: 20px;
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
    text-align:justify;
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
  .subrayado{
    text-decoration:underline;
  }
  .lista_guion{
    list-style-type: initial;
  }

  .negrita{
    font-weight: bold;
  }

  .sin_lista{
    list-style:none;
  }
  .letra_12{
    font-size:12px;
  }
  .margin_top_50{
    margin-top: 50px;
  }

  .margin_top_100{
    margin-top: 100px;
  }

  .margin_lista_faltas > li{
    margin-top:10px;
  }


  .base_firma_p{
    text-decoration:overline;
  }

  .bloque_final_conocimiento{
    width: 90%;
    margin:0 auto;
    margin-top:100px;
    text-align:justify;
  }

  </style>
</head>
<body class="letra_12">

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
    <h3 class="subrayado">{{$movimiento->titulo_documento}}</h3>
  </div>
  <div class="fila parrafo">
    <h5>{{$movimiento->nombre_empleado}}</h5>
    <h5>Rut: {{$movimiento->rut_empleado}}</h5>
    <h5>PRESENTE</h5>
  </div>
  <div class="fila parrafo">
    <p style="text-decoration:underline;">Para su conocimiento:</p>

    <p>La empresa Inv. Bosques del Mauco S.A. ha decidido amonestarlo por medio de
      la presente, en atención a las infracciones que a continuación se describen.</p>

      <p>En conformidad a lo dispuesto en la legislación laboral, y en particular de acuerdo
        con lo dispuesto en el Título XVI, artículo 52, Nro 2 del Reglamento Interno de Inv.
        Bosques del Mauco S.A. todos los trabajadores de nuestra empresa deben cumplir la obligación de ingresar a prestar servicios en la hora señalada por el empleador.-</p>

        <p>El día	{{$movimiento->fecha_inicio_movimientos->format('j F \\d\\e\\l Y')}}
          , usted registó su ingreso en el correspondiente  Libro  de  Asistencia  a  las	{{Carbon\Carbon::createFromFormat("H:i:s",$movimiento->hora_llegada)->format('H:i')}} horas
          , cuando su jornada de trabajo comenzaba a las  {{Carbon\Carbon::createFromFormat("H:i:s",$movimiento->hora_entrada)->format('H:i')}} hrs.-</p>

          <p>El retraso señalado constituye una infración a las obligaciones contenidas en su
            contrato de trabajo y en el Reglamento Interno ya citado.  A su vez , genera pro-
            blemas en el normal funcionamiento de la empresa y afecta el trabajo de las   de-
            más personas que prestan servicios en dependencias de la empresa.-</p>

            <p>Le recordamos que la reiteración de incumplimientos como los señalados puede
              configurar una causal de despido de acuerdo con lo dispuesto en el artículo
              160 nro 7 del Código del Trabajo.-
            </p>
          </div>
          <div class="fila bloque_firmas bloque_final_conocimiento">
            <div class="firma_encargado" >
              <p>p.p. INVERS. BOSQUES DEL MAUCO S.A.</p>
            </div>
            <div class="firma_empleado margin_top_100">
              <p class="base_firma_p">TOMO CONOCIMIENTO</p>
            </div>
          </div>
        </body>
        </html>
