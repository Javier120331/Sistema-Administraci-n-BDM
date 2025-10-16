<?php

namespace App\Http\Controllers;
use Excel;
use Response;
use Illuminate\Http\Request;
use App\Imports\VacacionesAriztiaImport;
use App\Imports\ProgresivasAriztiaImport;
use App\Imports\LicenciasAriztiaImport;
use App\Imports\PermisosAriztiaImport;

class ImportacionAriztiaController extends Controller
{
    private $directorioPadre = 'Suelditas/HistAriztia/';

    public function importDataHistorica(){
      set_time_limit(0);
      try{
        Excel::import(new ProgresivasAriztiaImport
        , $this->directorioPadre.'progresivas.csv');
        Excel::import(new LicenciasAriztiaImport
        , $this->directorioPadre.'licencias.csv');
        Excel::import(new VacacionesAriztiaImport
        , $this->directorioPadre.'vacaciones.csv');
        Excel::import(new PermisosAriztiaImport
        , $this->directorioPadre.'permisos.csv');
        Excel::import(new PermisosAriztiaImport
        , $this->directorioPadre.'sindicalesOtros.csv');

        return Response::json(true);

     }catch(\Illuminate\Database\QueryException $ex){
       return Response::json(false);
     }catch(Exception $ex){
       return Response::json(false);
     }

    }
    public function importVacaciones(){
       set_time_limit(0);
       try{
         Excel::import(new VacacionesAriztiaImport
         , $this->directorioPadre.'vacaciones.csv');
         return Response::json(true);

      }catch(\Illuminate\Database\QueryException $ex){
        return Response::json(false);
      }catch(Exception $ex){
        return Response::json(false);
      }

    }
    public function importPermisos(){
       set_time_limit(0);
       try{
         Excel::import(new PermisosAriztiaImport
         , $this->directorioPadre.'permisos.csv');
         return Response::json(true);
      }catch(\Illuminate\Database\QueryException $ex){
        return Response::json(false);
      }catch(Exception $ex){
        return Response::json(false);
      }

    }

    public function importSindicalesOtros(){
       set_time_limit(0);
       try{
         Excel::import(new PermisosAriztiaImport
         , $this->directorioPadre.'sindicalesOtros.csv');
         return Response::json(true);
      }catch(\Illuminate\Database\QueryException $ex){
        return Response::json(false);
      }catch(Exception $ex){
        return Response::json(false);
      }

    }

    public function importProgresivas(){
      set_time_limit(0);
      try{
        Excel::import(new ProgresivasAriztiaImport
        , $this->directorioPadre.'progresivas.csv');
        return Response::json(true);

      }catch(\Illuminate\Database\QueryException $ex){
        return Response::json(false);
     }catch(Exception $ex){
       return Response::json(false);
     }
    }
    public function importLicencias(){
      set_time_limit(0);
      try{
        Excel::import(new LicenciasAriztiaImport
        , $this->directorioPadre.'licencias.csv');
        return Response::json(true);

      }catch(\Illuminate\Database\QueryException $ex){
        return Response::json(false);
     }catch(Exception $ex){
       return Response::json(false);
     }
    }
}
