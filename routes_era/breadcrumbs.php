<?php

// Home
Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', action('HomeController@index'));
});

Breadcrumbs::register('usuarios', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Usuarios', action('UsuarioController@index'));
});
// Home > Usuarios > Registrar
Breadcrumbs::register('registroUsuarios', function($breadcrumbs)
{
    $breadcrumbs->parent('usuarios');
    $breadcrumbs->push('Registrar', action('Auth\RegisterController@showRegistrationForm'));
});
// Home > Usuarios > Editar
Breadcrumbs::register('editarUsuario', function($breadcrumbs)
{
    $breadcrumbs->parent('usuarios');
    $breadcrumbs->push('Editar', '');
});

// Home > Usuarios > Usuarios Deshabilitados
Breadcrumbs::register('usuariosDeshabilitados', function($breadcrumbs)
{
    $breadcrumbs->parent('usuarios');
    $breadcrumbs->push('Usuarios Deshabilitados', action('UsuarioController@deshabilitados'));
});


// Home > Importación
Breadcrumbs::register('importacion', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Importación', action('ImportacionController@index'));
});

// Home > Liquidación
Breadcrumbs::register('liquidaciones', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Liquidaciones', action('LiquidacionController@index'));
});
//Home > Vacaciones
Breadcrumbs::register('vacaciones', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Vacaciones', action('VacacionesController@index'));
});

//Home > Vacaciones > Ingresar
Breadcrumbs::register('ingresarVacaciones', function($breadcrumbs)
{
    $breadcrumbs->parent('vacaciones');
    $breadcrumbs->push('Ingresar', action('VacacionesController@create'));
});

//Home > Vacaciones > Consulta
Breadcrumbs::register('consultaVacaciones', function($breadcrumbs)
{
    $breadcrumbs->parent('vacaciones');
    $breadcrumbs->push('Consulta', action('VacacionesController@consulta'));
});

//Home > Vacaciones Progresivas
Breadcrumbs::register('vacacionesProgresivas', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Vacaciones Progresivas', action('VacacionesController@indexProgresivas'));
});
//Home > Vacaciones Progresivas > Ingresar Progresivas
Breadcrumbs::register('ingresarProgresivas', function($breadcrumbs)
{
    $breadcrumbs->parent('vacacionesProgresivas');
    $breadcrumbs->push('Ingresar Progresivas', action('VacacionesController@agregarVacacionesProgresivas'));
});
//Home > Movimientos
Breadcrumbs::register('movimientos', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Movimientos', action('MovimientosController@index'));
});
//Home > Movimientos > Ingresar
Breadcrumbs::register('ingresarMovimientos', function($breadcrumbs)
{
    $breadcrumbs->parent('movimientos');
    $breadcrumbs->push('Ingresar', action('MovimientosController@create'));
});

//Home > Feriados
Breadcrumbs::register('feriados', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Feriados', action('FeriadosController@index'));
});
// Home > Licencias
Breadcrumbs::register('licencias', function($breadcrumbs)
{
    $breadcrumbs->parent('movimientos');
    $breadcrumbs->push('Licencias', action('LicenciasController@index'));
});
// Home > Licencias > crear
Breadcrumbs::register('registrarLicencia', function($breadcrumbs)
{
    $breadcrumbs->parent('licencias');
    $breadcrumbs->push('Registrar', action('LicenciasController@create'));
});

//Home > Finiquitos
Breadcrumbs::register('finiquitos', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Finiquitos', action('FiniquitosController@index'));
});

//Home > Finiquitos > crear
Breadcrumbs::register('ingresarFiniquito', function($breadcrumbs)
{
    $breadcrumbs->parent('finiquitos');
    $breadcrumbs->push('Registrar', action('FiniquitosController@create'));
});


//Home > HistorialAsistencia
Breadcrumbs::register('historialAsistencia', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Historial Asistencia', action('HistorialAsistenciaController@index'));
});