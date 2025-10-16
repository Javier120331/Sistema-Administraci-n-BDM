<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Comprobante de Vacaciones">
    <meta name="author" content="Bosques del Mauco">
    <title>Inversiones Bosques del Mauco -  Comprobante de Vacaciones</title>
    <style type="text/css">
      body{
        font-size: 12px;
      }
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
        margin-top: 50px;
      }
      .lista_detalle > li{
        margin-top: 10px;
        font-size:14px;
      }
      .lista_detalle{
        margin:0px;
        padding:0px;
        margin-top: 50px;
        list-style: none;
      }
      .letra_15{
        font-size: 15px;
      }
      .letra_14{
        font-size: 14px;
      }
      .letra_16{
        font-size:16px;
      }

      .bloque_firmas{

        width: 100%;
        margin:0 auto;
        margin-top:150px;
      }
      .bloque_firma_notario{

        width: 50%;
        margin:0 auto;
        margin-top:200px;
        margin-left: 32%;
      }
      .bloque_rut{
        margin-top:20px;
      }
      .bloque_firmas, .bloque_rut{
        font-size:14px;
      }
      .bloque_firmas  .base_firma{
        width: 160px;
        display:inline-block;
        border-bottom: 1px;
        border-style:solid;
        border-color: black;

      }

      .bloque_firma_notario .base_firma{
        width: 200px;
        display:inline-block;
        border-bottom: 1px;
        border-style:solid;
        border-color: black;
      }
      .bloque_izq{
        float:left;
        width:40%;
        margin-left:10%;
        text-align:justify;
      }
      .bloque_der{
        float:right;
        width:40%;
      }
      .bloque_firma_notario > div:last-child{
        margin-left: 20%;
      }
      .tabla_vacaciones  tr th,.tabla_vacaciones  tr td {
        padding:5px;
      }

    </style>
</head>
<body>

  <div class="fila">
      <div class="div_logo">
          <img src="{{ $imagen }}" />
      </div>
      <div class="div_area">
        <h3></h3>
      </div>
  </div>
  <div class="fila centrado_texto">
    <h3 align=center>BOSQUES DEL MAUCO</h3>
    <h3 align=center>COMPROBANTE DE VACACIONES</h3>
  </div>
  <div class="fila parrafo">
    <p>Don(a) {{$periodo->empleado()->first()->nombre}}
      , RUT {{App\Utilidades\RutUtils::getRutDeFontana($periodo
        ->empleado()->first()->rut)}} hara uso de
    su feriado correspondiente al periodo contractual desde el {{ $periodo->fecha_inicio->format('l d \\d\\e F \\d\\e\\l Y')}} al {{ $periodo->fecha_termino->format('l d \\d\\e F \\d\\e\\l Y')}} segun el siguiente detalle:</p>
    <p>
      <table class="tabla_vacaciones">
        <tr>
          <th>Dias Base</th>
          <td>: 15.00</td>
        </tr>
        <tr>
          <th>Dias Feriado Progresivo</th>
          <td>: {{$resumen->dias_feriado_progresivo}}</td>
        </tr>
        <tr>
          <th>Dias otorgados a cuenta</th>
          <td>: {{$vacacion->dias_ya_autorizados}}</td>
        </tr>
        <tr>
          <th>Saldos al periodo</th>
          <td>: {{$resumen->saldos_al_periodo}}</td>
        </tr>
        <tr>
          <th>Fecha Inicio</th><td>: {{$vacacion->fecha_inicio->format('d/m/Y')}}</td>
          <th>Fecha Termino</th><td>: {{$vacacion->fecha_termino->format('d/m/Y')}}</td>
        </tr>
        <tr>
          <th>Días Autorizados</th><td>: {{$vacacion->cantidad_dias}}</td>
        </tr>
        <tr>
          <th>Saldo Pendientes</th><td>: {{ $resumen->saldo_pendiente}} </td>
        </tr>
        <tr>
          <th>Jornada</th><td>: Dia completo</td>
        </tr>
        <tr>
          <th>Fecha reinicio labores</th><td>: {{$resumen->fecha_retorno
              ->format('d/m/Y')}}</td>
        </tr>
      </table>
    </p>
  </div>
  <div class="bloque_firmas ">
    <div class="fila  bloque_izq">
      <div >
        <span class="base_firma"></span>
      </div>
      <div>
        <span>FIRMA DEL EMPLEADOR</span>
      </div>

    </div>
    <div class="fila  bloque_der">
      <div >
        <span class="base_firma"></span>
      </div>
      <div>
        <span>FIRMA DEL TRABAJADOR</span>
      </div>
    </div>
  </div>
</body>
</html>
