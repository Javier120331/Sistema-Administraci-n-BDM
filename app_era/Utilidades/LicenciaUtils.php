<?php
namespace App\Utilidades;
use App\MovimientoAsistencia;

class LicenciaUtils{

  public static $TIPOS_LICENCIAS = [
    1 =>"Día Completo",
    2 => 'Medio Día'
  ];

  public static $CAUSAS = [
    1=> "ENFERMEDAD O ACCIDENTE COMUN",
    2=> "PRORROGA MEDICINA PREVENTIVA",
    3=> "LICENCIA MATERNA PRE Y POST NATAL",
    4=> "ENFERMEDAD GRAVE NIÑO MENOR DE 1 AÑO",
    5=> "ACCIDENTE DEL TRABAJO O DEL TRAYECTO",
    6=> "ENFERMEDAD PROFESIONAL",
    7=> "PATOLOGIA DEL EMBARAZO"
  ];

}
