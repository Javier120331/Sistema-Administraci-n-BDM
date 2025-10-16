<?php

  namespace App\Utilidades;

  class ImagenUtils{

    public static function getLogoEmpresa(){
      $path = public_path('img/logo.png');

      $type = pathinfo($path, PATHINFO_EXTENSION);
      $data = file_get_contents($path);
      $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

      return $base64;
    }

  }
