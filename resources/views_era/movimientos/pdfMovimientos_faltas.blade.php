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
      .margin_lista_faltas > li{
        margin-top:10px;
      }

      .firma_encargado{
        float:left;
      }
      .firma_empleado{
        float:right;
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
    <p  class="margin_top_50">
      <ul class="sin_lista">
        <li>DE	{{$movimiento->nombre_encargado}}</li>
        <li>A  	{{$movimiento->nombre_empleado}}</li>
        <li>RUT	{{$movimiento->rut_empleado}}</li>
        <li>FECHA {{$movimiento->fecha_documento->format('j F \\d\\e\\l Y')}}</li>
        <li>REF.: <span class="subrayado">Amonestación por faltas que indica</span></li>
      </ul>
    </p>
    <p class="margin_top_50">
      @if($movimiento->fecha_termino_movimientos != null )
        Como es de su conocimiento usted no se presentó a trabajar desde el  día {{$movimiento->fecha_inicio_movimientos->format('j F \\d\\e\\l Y')}}
          hasta el día {{$movimiento->fecha_termino_movimientos->format('j F \\d\\e\\l Y')}} , sin justificación.-
      @else
        Como es de su conocimiento usted no se presentó a trabajar el día {{$movimiento->fecha_inicio_movimientos->format('j F \\d\\e\\l Y')}}
        , sin justificación.-
      @endif

    </p>
    <p>
      En conformidad a lo dispuesto en el Título XX , artículo 58  del Reglamento  Interno de Inv. Bosques del Mauco.-
    </p>
    <p>
      Las ausencias injustificadas al trabajo, dentro de un período de doce meses, serán sancionadas de la siguiente manera;
    </p>
    <p>
      <ul class="lista_guion margin_lista_faltas">
        <li>Primera a tercera inasistencia:
          <span class="negrita">Amonestación con multa.</span></li>
        <li>Cuarta o más inasistencias:
        <span class="negrita">Serán consideradas como incumplimiento grave de las obligaciones que impone el contrato.-</span>
        </li>
      </ul>
    </p>
    <p class="margin_top_50">
    Independientemente de lo establecido en este artículo,  la Empresa está  facultada para aplicar, cuando proceda, las sanciones establecidas en el artículo 160, causal Nro 3 y 7 del Código del Trabajo sobre inasistencias injustificadas al trabajo, de dos días seguidos, dos lunes en el mes, o  un total de  tres días durante  igual  período de tiempo;   Asimismo, la falta injustificada, o sin aviso previo de  parte  del trabajador que tuviere a cargo una actividad, faena, o máquina cuyo abandono o paralización signifique perturbación grave en la marcha de la obra, e incumplimiento grave de las obligaciones que impone el contrato, que dan lugar al término del Contrato de trabajo sin derecho a Indemnización alguna.-
    </p>
    <p>Atentamente,</p>
  </div>
  <div class="fila bloque_firmas bloque_final_conocimiento">
    <div class="firma_encargado" >
      <p>{{$movimiento->nombre_encargado}}</p>
      <p>Jefe de Personal</p>
      <p>cc CARPETA PERSONAL</p>
      <p>INSPECCION DEL TRABAJO</p>
    </div>
    <div class="firma_empleado">
      <p class="base_firma_p">TOMO CONOCIMIENTO</p>
    </div>
  </div>
</body>
</html>
