<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Acuerdo de Permiso de ">
    <meta name="author" content="Bosques del Mauco">
    <title>Inversiones Bosques del Mauco -  TRANSACCION, RENUNCIA, RECIBO Y FINIQUITO</title>
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
        margin-top: 30px;
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
        font-size:16px;
      }
      .bloque_firmas  .base_firma{
        width: 200px;
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

      .haberes_div h6 ,.descuentos_div h6 , .monto_pagar_div h6{
        font-size: 14px;
        margin:0px;
        margin-top:10px;
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
    <h3>{{$finiquito->nombre_empresa}}</h3>
    <h5>FINIQUITO</h5>
  </div>
  <div class="fila parrafo">
    <p>En {{$finiquito->comuna_empresa}}, a {{$finiquito->fecha_documento->format('l d \\d\\e F \\d\\e\\l Y')}}
     entre {{$finiquito->nombre_empresa}},
     Rol Unico Tributario N. {{App\Utilidades\RutUtils::getRutDeFontana($finiquito->rol_empresa)}}, empresa representada por Don(a)
     {{$finiquito->nombre_empleador}}, cedula nacional de identidad N. {{App\Utilidades\RutUtils::getRutDeFontana($finiquito->rut_empleador)}},
     ambos domiciliados en {{$finiquito->domicilio_empleador}} , en adelante la empresa o la ex empleadora;
     y don {{$finiquito->nombre_empleado}},
     {{$finiquito->cargo_empleado}}, cedula nacional de identidad N. {{App\Utilidades\RutUtils::getRutDeFontana($finiquito->rut_empleado)}},
     domiciliado en {{$finiquito->domicilio_empleado}} ,
     comuna de {{$finiquito->comuna_empleado}} , en adelante "el Trabajador", se acuerda el siguiente finiquito:
    </p>
    <p >
      <ol class="lista_detalle">
          <li> <p> <b>PRIMERO:</b> El Trabajador declara haber prestado servicios  para  la  empresa
            desde el {{$finiquito->fecha_inicio_contrato->format('l d \\d\\e F \\d\\e\\l Y')}} , y hasta  el {{$finiquito->fecha_finiquito->format('l d \\d\\e F \\d\\e\\l Y')}}, fecha
            esta  última  de  terminación  de  sus  servicios por  la  causal de {{$finiquito->causa_finiquito}}  establecida en el {{$finiquito->articulo_finiquito}} {{$finiquito->numero_articulo_finiquito }}
            del Código  del Trabajo, causal que el trabajador acepta y reconoce  en este acto.</p>
          </li>
        <li> <div class="tercero_div">
          <p>
           <b>SEGUNDO:</b> El trabajador declara   recibir  en  este  acto, a  su  entera  satisfacción, de  parte  del Empleador, la suma total de
            ${{App\Utilidades\NumerosUtils::getMoneda($finiquito->total_pagar)}} conforme al siguiente desglose :</p>

            </div>
           <div class="haberes_div letra_15">
             <h6>Haberes:</h6>
             <table>
               @if(isset($finiquito->mes_aviso))
               <tr>
                 <th>INDEMNIZACION SUSTITUTIVA AVISO PREVIO:</th><td> $ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->mes_aviso)}}</td>
               </tr>
              @endif
              <tr>
                <th>INDEMNIZACION AÑOS DE SERVICIO ({{$finiquito->anios_servicio}} años ):</th><td> $ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->indemnizacion_anios_servicio)}}</td>
              </tr>
              <tr>
                <th>DIAS HABILES VACACIONES ({{$finiquito->dias_habiles_vacaciones}}) :</th><td> $ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->total_dias_habiles_vacaciones)}}</td>
              </tr>
              <tr>
                <th>DIAS INHABILES VACACIONES ({{$finiquito->dias_inhabiles}}) :</th><td> $ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->total_dias_inhabiles)}}</td>
              </tr>
             </table>
           </div>
           <div class="descuentos_div letra_15">
             <h6>Descuentos:</h6>

               <table>
                @if(isset($finiquito->seguro_desempleo) and $finiquito->seguro_desempleo>0 )
                 <tr>
                   <th>SEGURO DE DESEMPLEO:</th><td>$ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->seguro_desempleo)}}</td>
                 </tr>
                @endif
                @if( isset($finiquito->prestamo_empresa) and $finiquito->prestamo_empresa > 0)
                 <tr>
                   <th>PRESTAMO EMPRESA:</th><td>$ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->prestamo_empresa)}}</td>
                 </tr>
                 @endif
                 @if( isset($finiquito->descuento_sobregiro) and $finiquito->descuento_sobregiro > 0)
                  <tr>
                    <th>DESCUENTO SOBREGIRO:</th><td>$ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->descuento_sobregiro)}}</td>
                  </tr>
                @endif
                @if( isset($finiquito->descuento_convenios) and $finiquito->descuento_convenios > 0)
                 <tr>
                   <th>DESCUENTO CONVENIOS:</th><td>$ {{ App\Utilidades\NumerosUtils::getMoneda($finiquito->descuento_convenios)}}</td>
                 </tr>
                 @endif
               </table>

           </div>
           <div class="monto_pagar_div letra_15">
              <h6>Monto a Pagar: $ {{App\Utilidades\NumerosUtils::getMoneda($finiquito->total_pagar)}}</h6>
           </div>
        </li>
        <li>
          <p>

<b>TERCERO:</b> El trabajador  deja  constancia  que  durante todo  el  tiempo  que  prestó  servicios  para el Empleador  recibió  de éste,  correcta  y  oportunamente,  el  total  de  las  remuneraciones  convenidas  de  acuerdo  con  su contrato  de  trabajo y correspondiente instrumento colectivo, pago  de asignaciones  familiares autorizadas  por  la  respectiva  Institución  de Previsión, horas  extraordinarias  cuando  las  trabajó, feriados legales, bonificaciones , cotizaciones previsionales, etc,  y  que  nada  se  le  adeuda  por  los conceptos  antes  indicados  ni  por  ningún  otro, sea  de  origen  legal  o  contractual  derivado  de la  prestación  de  sus  servicios, y motivo  por  el  cual,  no  teniendo  cargo  alguno  que  formular en  contra  de  su Empleador,  le  otorga el más  amplio  y  total  finiquito, sin reserva alguna, declaración  que  formula libre  y  espontáneamente, en perfecto y cabal conocimiento de cada uno  y  todos   sus  derechos.
Asimismo, el  trabajador deja constancia de que entre las partes existió siempre un trato digno y respetuoso, no habiendo el Empleador incurrido en vulneración alguna de derechos fundamentales, como asimismo que el empleador cumplió cabalmente con su obligación de protección de la vida,  seguridad y prevención que establece el art. 184 y sgtes. del Código del Trabajo, Ley 16.744 y normas complementarias,  por todo lo cual el trabajador viene en renunciar a toda acción o derecho indemnizatorio que pudiere corresponderle respecto de su nombrado empleador por estos conceptos y por cualquier otro que tuviere relación con el contrato de trabajo que existió entre las partes y correspondiente instrumento colectivo, incluída  la acción de nulidad de despido, de despido injustificado, de tutela, daño moral, lucro cesante, diferencias de remuneración y de indemnizaciones de toda índole, renuncia que resulta válida por realizarse con posterioridad al término de dicho contrato conforme lo autoriza el art. 5 inciso 2º. del Código del Trabajo.
          </p>
        </li>
        <li>
          <p><b>CUARTO:</b> A fin de precaver todo litigio eventual, las partes otorgan al presente finiquito el carácter de transacción conforme al art. 2446 y sgtes del Código Civil , con pleno efecto de cosa juzgada
        </p>
      </li>
      <li>
        <p>
            <b>QUINTO:</b> El trabajador hace extensivo el presente finiquito, renuncia y transacción a toda persona natural o jurídica vinculada con el Empleador y que pudiere tener algún grado de responsabilidad, sea solidaria, subsidiaria o simplemente conjunta, respecto
de obligaciones del Empleador para con el Trabajador.
      </p>
    </li>
    <li><p>
Para  constancia  firman  las  partes  el  presente  finiquito  en  tres ejemplares,  quedando dos  de  ellos en poder del empleador y otro en poder  el trabajador.-
</p>
        </li>
      </ol>
    </p>
  </div>
  <div class="bloque_firmas letra_16">
    <div class="fila  bloque_izq">
      <div >
        <span class="base_firma"></span>
      </div>
      <div>
        <span>FIRMA DEL EMPLEADOR</span>
      </div>
      <div class="fila bloque_rut letra_16">
        <span>RUT Nro.</span>&nbsp;<span>{{App\Utilidades\RutUtils::getRutDeFontana($finiquito->rol_empresa)}}</span>
      </div>
    </div>
    <div class="fila  bloque_der">
      <div >
        <span class="base_firma"></span>
      </div>
      <div>
        <span>FIRMA DEL TRABAJADOR</span>
      </div>
      <div class="fila bloque_rut letra_16">
        <span>RUT Nro.</span>&nbsp;<span>{{App\Utilidades\RutUtils::getRutDeFontana($finiquito->rut_empleado)}}</span>
      </div>
    </div>
  </div>
  <br />
  <div class="bloque_firma_notario letra_16">
    <div >
      <span class="base_firma"></span>
    </div>
    <div>
      <span>NOTARIO</span>
    </div>
  </div>


</body>
</html>
