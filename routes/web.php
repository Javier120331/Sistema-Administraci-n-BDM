<?php
//Muestra todos los errores en PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit','128M');
error_reporting(E_ALL);
//Para retrocompatibilidad con otras versiones de PHP
if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    // Ignores notices and reports all other kinds... and warnings
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    // error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}
error_reporting(E_ALL ^ E_DEPRECATED);
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//WORKAROUND:
//Retrocompatibilidad con Carbon1
//Ahora carbon soporta timezone, lo cual en el formato de salida
//destruye todo lo hecho al utilizar la librería moment js
//Esto se asegura que el formato de serialización de Carbon 2
//sea el de Carbon1
Carbon\Carbon::serializeUsing(function ($date) {
    return [
        'date' => $date->toDateTimeString(),
        'timezone' => $date->tzName,
    ];
});
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
//WORKAROUND:Logout cambió a POST en la ultima versión, esto solventa
//WORKAROUND:la inconsistencia con la nueva
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index');
Route::get('/', 'HomeController@home');

Route::auth();
Route::get('home', 'HomeController@index');
//Importación
Route::get('importacion', 'ImportacionController@index');
Route::get('importacion/upload'
  , 'ImportacionController@subirArchivosLiquidacion');
Route::post('importacion/upload'
    , 'ImportacionController@subirArchivosLiquidacion');
Route::post('importacion/upload_remuneraciones'
        , 'ImportacionController@subirArchivosRemuneraciones');
Route::get('importacion/upload_remuneraciones'
        , 'ImportacionController@subirArchivosRemuneraciones');

Route::post('importacion/rollback_liquidacion'
            , 'ImportacionController@efectuarRollbackLiquidacion');
//Liquidación
Route::get('liquidacion', 'LiquidacionController@index');
Route::post("liquidacion", 'LiquidacionController@post');
Route::get("liquidacion/grupo/cosechadores/{id}", 'LiquidacionController@getCosechadoresByGrupo');

//Usuarios
Route::resource('usuario', 'UsuarioController', ['only' => 'index']);
Route::get('usuario/{id}/edit', 'UsuarioController@edit');
Route::get('usuario/getAjaxData', 'UsuarioController@getAjaxData');
Route::get('usuario/deshabilitados/getAjaxData',
      'UsuarioController@getAjaxDataDeshabilitados');
Route::get('usuario/deshabilitados', 'UsuarioController@deshabilitados');

Route::post('usuario/{id}/habilitar', 'UsuarioController@habilitar');
Route::post('usuario/{id}/deshabilitar', 'UsuarioController@deshabilitar');
Route::get('usuario/{id}/edit', 'UsuarioController@edit');
Route::patch('usuario/{id}', 'UsuarioController@update');

//Buscador de Trabajadores
Route::get('buscador-trabajadores', 'BuscadorTrabajadoresController@index')->name('buscador.index');
Route::post('buscador-trabajadores/buscar', 'BuscadorTrabajadoresController@buscar');
Route::get('buscador-trabajadores/{tipo}/{id}', 'BuscadorTrabajadoresController@detalle')->name('buscador.detalle');

//Movimientos
Route::get("movimientos", 'MovimientosController@index');
Route::get("movimientos/create", 'MovimientosController@create');
Route::post("movimientos/store", 'MovimientosController@store');
Route::get("movimientos/download/{id}", 'MovimientosController@download');
Route::get('movimientos/getAjaxData', 'MovimientosController@getAjaxData');
Route::get("movimientos/getFechasMovimiento"
  , 'MovimientosController@getFechasMovimiento');
Route::post("movimientos/anular"
            , 'MovimientosController@anular');
//Vacaciones
Route::get("vacaciones", 'VacacionesController@index');
Route::get("vacaciones/consulta", 'VacacionesController@consulta');
Route::get("vacaciones/create", 'VacacionesController@create');
Route::post("vacaciones/store", 'VacacionesController@store');
Route::get("vacaciones/getAjaxData"
    , 'VacacionesController@getAjaxData');
Route::get("vacaciones/getAjaxDataEmpleado"
  , 'VacacionesController@getAjaxDataEmpleado');
Route::get("vacaciones/getAjaxDataConsulta"
        , 'VacacionesController@getAjaxDataConsulta');
Route::get("vacaciones/getCantidadDiasRecomendados"
        , 'VacacionesController@getCantidadDiasRecomendados');
Route::post("vacaciones/anular"
          , 'VacacionesController@anular');
Route::get("vacaciones/download/{id}", 'VacacionesController@download');

//Periodos
Route::get("vacaciones/getAjaxDataPeriodos"
    , 'VacacionesController@getAjaxDataPeriodos');

//Vacaciones Progresivas
Route::get("vacaciones/ver_progresivas"
  , 'VacacionesController@indexProgresivas');
Route::get("vacaciones/getAjaxDataProgresivas"
    , 'VacacionesController@getAjaxDataProgresivas');
Route::get("vacaciones/create_progresivas"
  , 'VacacionesController@agregarVacacionesProgresivas');
Route::post("vacaciones/store_progresivas"
  , 'VacacionesController@storeVacacionesProgresivas');

//Feriados
Route::get("feriados", "FeriadosController@index");
Route::post("feriados/store", "FeriadosController@store");
Route::post("feriados/importar", "FeriadosController@importar");
Route::get("feriados/get", "FeriadosController@getFeriados");
Route::post("feriados/delete", "FeriadosController@delete");
Route::get("feriados/getFechasReales", "FeriadosController@getFechasReales");
Route::get("feriados/getFechasRealesIncWeekend"
  , "FeriadosController@getFechasRealesIncWeekend");
//Licencias
Route::get("licencias", 'LicenciasController@index');
Route::get("licencias/create", 'LicenciasController@create');
Route::post("licencias/store", 'LicenciasController@store');
Route::get('licencias/getAjaxData', 'LicenciasController@getAjaxData');
Route::post("licencias/anular"
            , 'LicenciasController@anular');

//Finiquitos
Route::get("finiquitos", 'FiniquitosController@index');
Route::get("finiquitos/create", 'FiniquitosController@create');
Route::post("finiquitos/finiquitar/{idEmpleado}", 'FiniquitosController@finiquitar');
Route::post("finiquitos/cargarDatosFiniquito"
  , 'FiniquitosController@cargarDatosFiniquito');
Route::get("finiquitos/cargarDatosFiniquito"
    , 'FiniquitosController@cargarDatosFiniquito');
Route::post("finiquitos/getAjaxDataEmpleado"
  , 'FiniquitosController@getAjaxDataEmpleado');
Route::get('finiquitos/getAjaxData', 'FiniquitosController@getAjaxData');
Route::post('finiquitos/getVacacionesAjax'
  , 'FiniquitosController@getValorVacacionesAjax');
Route::get("finiquitos/download/{id}", 'FiniquitosController@download');
Route::post("finiquitos/anular"
            , 'FiniquitosController@anular');
Route::get("finiquitos/getAjaxDiasInhabiles"
                        , 'FiniquitosController@getAjaxInhabiles');
Route::post("finiquitos/getAjaxSueldoAniosServicio"
  , "FiniquitosController@getAjaxSueldoAniosServicio");
//importación ariztia
Route::get('ariztia/importVacaciones', 'ImportacionAriztiaController@importVacaciones');
Route::get('ariztia/importProgresivas', 'ImportacionAriztiaController@importProgresivas');
Route::get('ariztia/importLicencias', 'ImportacionAriztiaController@importLicencias');
Route::get('ariztia/importPermisos', 'ImportacionAriztiaController@importPermisos');
Route::get('ariztia/importSindicales', 'ImportacionAriztiaController@importSindicalesOtros');

Route::get('ariztia/importDataHistorica', 'ImportacionAriztiaController@importDataHistorica');
//Historial Asistencia
Route::get("historialAsistencia", 'HistorialAsistenciaController@index');
Route::post("historialAsistencia", 'HistorialAsistenciaController@generar');
